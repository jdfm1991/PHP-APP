<?php
ini_set('memory_limit', '-1');
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Principal extends Conectar{

    public function getDocumentosSinDespachar(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

$ffin = date('Y-m-d');
$dato = explode("-", $ffin); //Hasta
$aniod=$dato[0]; //año
$mesd=$dato[1]; //mes
$diad="01"; //dia
$fini=$aniod."-01-01";
$t=0;

$sql="SELECT NumeroD, Descrip, TipoFac from safact AS SA where DATEADD(dd, 0, DATEDIFF(dd, 0, SA.FechaE))
                        between '$fini' and '$ffin' and SA.TipoFac in ('A','C') and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR)) and SA.NumeroD not in (SELECT numeros FROM appfacturas_det  where TipoFac='A' or TipoFac='C') and SA.NumeroD not in (SELECT numerof FROM sanota) order by SA.NumeroD";
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPedidosSinFacturar(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT sf.numerod, sf.codvend, sv.descrip AS vendedor, sc.codclie as codclie,sc.descrip AS cliente, si.coditem,
                       si.descrip1, sp.marca, si.esunid, sf.signo*si.cantidad AS cantidad, sf.signo*si.totalitem / si.tasai AS totalitem,
                       se.Existen AS bultos, se.ExUnidad AS paquetes, sf.fechae
                FROM SACLIE AS SC
                    INNER JOIN SAFACT AS SF ON SC.CodClie = SF.CodClie
                    INNER JOIN SAITEMFAC AS SI ON SF.NumeroD = SI.NumeroD
                    INNER JOIN SAPROD AS SP ON SI.CodItem = SP.CodProd
                    INNER JOIN SAVEND AS SV ON sf.CodVend = sv.CodVend
                    INNER JOIN SAEXIS AS SE ON si.CodItem = SE.CodProd
                WHERE  (SF.NumeroD = SI.NumeroD AND SF.TipoFac = SI.TipoFac)
                  AND SC.CodClie = SF.CodClie AND SI.NroLineaC = 0
                  AND SF.TipoFac = 'F' AND sf.Monto <> 0 AND se.CodUbic = 01
                ORDER BY SF.FechaE DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_cxc_bs(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT SUM(SAACXC.Saldo) as saldo_bs
                FROM saacxc INNER JOIN saclie ON saacxc.codclie = saclie.codclie
                WHERE saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20')";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_cxc_bs_dolar(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT (saacxc.saldo/SAFACT.Tasa) as SaldoPendolar,
                (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
                from saacxc inner join saclie on saacxc.codclie = saclie.codclie inner join SAFACT on SAFACT.NumeroD= SAACXC.NumeroD 
                where saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') 
                order by saacxc.FechaE asc";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_cxc_dolares(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
       /* $sql = "SELECT SUM(total-abono) as saldo_dolares
                FROM SANOTA
                WHERE tipofac ='C' AND estatus in (0, 1)";*/
        
        $sql = "SELECT 
          SUM(SAACXC.Saldo) as saldo_dolares
           from DCONFISUR_PZO_D.dbo.saacxc inner join DCONFISUR_PZO_D.dbo.saclie on saacxc.codclie = saclie.codclie 
           where saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20')" ;

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

/////////////////////////  VENTAS ///////////////////////////////////////////////

    public function get_ventas_por_mes_fact($fechai, $fechaf) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY


        /*$sql="SELECT YEAR(CAST(SAITEMFAC.FechaE AS DATETIME)) anio, MONTH(CAST(SAITEMFAC.FechaE AS DATETIME)) mes,
                SAITEMFAC.TipoFac AS tipo,
                (SELECT tasa FROM SAFACT WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS factor,
                SAITEMFAC.TotalItem as total
                 FROM SAITEMFAC INNER JOIN saprod ON SAITEMFAC.coditem = saprod.codprod
                 INNER JOIN SAFACT ON SAITEMFAC.numerod = SAFACT.numerod AND SAITEMFAC.tipofac = SAFACT.tipofac WHERE
                 DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMFAC.FechaE)) between ? AND ? AND (SAITEMFAC.tipofac = 'A' OR SAITEMFAC.Tipofac = 'B') ORDER BY SAITEMFAC.fechae asc";
*/
        
        $sql="SELECT YEAR(CAST(SAFACT.FechaE AS DATETIME)) anio, MONTH(CAST(SAFACT.FechaE AS DATETIME)) mes,
                SAFACT.TipoFac AS tipo,
                (SELECT tasa FROM SAFACT fact WHERE fact.numerod = SAFACT.numerod AND fact.tipofac = SAFACT.tipofac) AS factor,
				 SAFACT.numeroD,
                (SAFACT.MtoTotal/1.16) as total,
				(SAFACT.Descto1+Descto2+DesctoP) as descuento
                 FROM SAFACT  WHERE
                 DATEADD(dd, 0, DATEDIFF(dd, 0, SAFACT.FechaE)) between ? and ? AND (SAFACT.tipofac = 'A' OR SAFACT.Tipofac = 'B') ORDER BY SAFACT.fechae asc";




//PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ventas_por_mes_nota($fechai, $fechaf) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY

       /* $sql="SELECT YEAR(CAST(sanota.fechae AS DATETIME)) anio, MONTH(CAST(sanota.fechae AS DATETIME)) mes,
                SAITEMNOTA.tipofac as tipo,
				SAITEMNOTA.esexento,
                (SELECT marca FROM SAPROD WHERE SAITEMNOTA.coditem = SAPROD.CodProd) AS marca,
                SAITEMNOTA.cantidad,
				SAITEMNOTA.esexento,
                (CASE SAITEMNOTA.esunidad WHEN 1 then 'PAQ' ELSE 'BULT' END) AS unid,
                (CASE SAITEMNOTA.esunidad WHEN 1 then cantidad ELSE cantidad*cantempaq END) AS paq,
                (CASE SAITEMNOTA.esunidad WHEN 1 then cantidad/cantempaq ELSE cantidad END) AS bul,
                (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.total ELSE SAITEMNOTA.total / 1.16 END) AS total
                 FROM SAITEMNOTA INNER JOIN saprod ON SAITEMNOTA.coditem = saprod.codprod
                 INNER JOIN sanota ON saitemnota.numerod = sanota.numerod AND saitemnota.tipofac = sanota.tipofac WHERE
                DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) between ? AND ?  AND (SAITEMNOTA.tipofac = 'C' OR SAITEMNOTA.Tipofac = 'D') AND  
                SANOTA.numerof =(SELECT numerof FROM sanota WHERE sanota.numerod = SAITEMNOTA.numerod AND sanota.tipofac = SAITEMNOTA.tipofac AND sanota.numerof = 0) ORDER BY SAITEMNOTA.fechae";
*/

            $sql="SELECT YEAR(CAST(sanota.fechae AS DATETIME)) anio, MONTH(CAST(sanota.fechae AS DATETIME)) mes,
                sanota.tipofac as tipo,
				sanota.numerod ,
                (sanota.total/1.16) AS total,
				descuento
                 FROM sanota  WHERE
                DATEADD(dd, 0, DATEDIFF(dd, 0, sanota.FechaE)) between ? and ?  AND (sanota.tipofac = 'C' OR sanota.Tipofac = 'D')  
                 ORDER BY sanota.fechae";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


     

/////////////////////////  BULTOS ///////////////////////////////////////////////

     public function get_bultos_por_mes_fact($fechai, $fechaf) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
       /* $sql = "SELECT YEAR(CAST(itemfact.FechaE AS DATETIME)) anio, MONTH(CAST(itemfact.FechaE AS DATETIME)) mes,
                       SUM(cantidad) as total
                FROM SAFACT fact
                         INNER JOIN SAITEMFAC itemfact ON itemfact.NumeroD = fact.NumeroD
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fact.FechaE)) BETWEEN ? AND ? AND itemfact.tipofac IN ('A','B')
                  AND fact.NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))
                GROUP BY YEAR(CAST(itemfact.FechaE AS DATETIME)), MONTH(CAST(itemfact.FechaE AS DATETIME))
                ORDER BY mes ASC";*/

                $sql="SELECT
YEAR(CAST(SAITEMFAC.FechaE AS DATETIME)) anio, MONTH(CAST(SAITEMFAC.FechaE AS DATETIME)) mes,
                SAITEMFAC.TipoFac AS tipo,
                SAITEMFAC.cantidad,
                (CASE SAITEMFAC.EsUnid WHEN 1 then 'PAQ' ELSE 'BULT' END) AS unid,
                (CASE SAITEMFAC.EsUnid WHEN 1 then cantidad/cantempaq ELSE cantidad END) AS bul,
                SAITEMFAC.fechae
                 FROM SAITEMFAC INNER JOIN saprod ON SAITEMFAC.coditem = saprod.codprod
                 INNER JOIN SAFACT ON SAITEMFAC.numerod = SAFACT.numerod AND SAITEMFAC.tipofac = SAFACT.tipofac WHERE
                 DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMFAC.FechaE)) between ? AND ? AND (SAITEMFAC.tipofac = 'A' OR SAITEMFAC.Tipofac = 'B') ORDER BY SAITEMFAC.fechae asc
";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_bultos_por_mes_nota($fechai, $fechaf) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
       /* $sql = "SELECT YEAR(CAST(itemnota.FechaE AS DATETIME)) anio, MONTH(CAST(itemnota.FechaE AS DATETIME)) mes,
                       SUM(cantidad) as total
                FROM SANOTA nota
                    INNER JOIN SAITEMNOTA itemnota ON itemnota.numerod = nota.numerod
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, itemnota.FechaE)) BETWEEN ? AND ? AND nota.tipofac in ('C','D') AND numerof = '0'
                GROUP BY YEAR(CAST(itemnota.FechaE AS DATETIME)), MONTH(CAST(itemnota.FechaE AS DATETIME))
                ORDER BY mes ASC";
*/

$sql="SELECT YEAR(CAST(SAITEMNOTA.FechaE AS DATETIME)) anio, MONTH(CAST(SAITEMNOTA.FechaE AS DATETIME)) mes,
                SAITEMNOTA.cantidad,
                SAITEMNOTA.TipoFac AS tipo,
                (CASE SAITEMNOTA.esunidad WHEN 1 then 'PAQ' ELSE 'BULT' END) AS unid,
                (CASE SAITEMNOTA.esunidad WHEN 1 then cantidad/cantempaq ELSE cantidad END) AS bul,
                SAITEMNOTA.fechae
                 FROM SAITEMNOTA INNER JOIN saprod ON SAITEMNOTA.coditem = saprod.codprod
                 INNER JOIN sanota ON saitemnota.numerod = sanota.numerod AND saitemnota.tipofac = sanota.tipofac WHERE
                 DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) between ? AND ? AND (SAITEMNOTA.tipofac = 'C' OR SAITEMNOTA.Tipofac = 'D') AND  
                SANOTA.numerof =(SELECT numerof FROM sanota WHERE sanota.numerod = SAITEMNOTA.numerod AND sanota.tipofac = SAITEMNOTA.tipofac AND sanota.numerof = 0) ORDER BY SAITEMNOTA.fechae";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_inventario_valorizado($alm) {
        $i = 0;
        $cond = $depo = "";
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        if (count($alm) > 0) {
            $aux = "";
            //se contruye un string para listar los depositvos seleccionados
            //en caso que no haya ninguno, sera vacio
            foreach ($alm as $num)
                $aux .= " or exis.codubic = ?";

            //armamos una lista de los depositos, si no existe ninguno seleccionado no se considera para realizar la consulta
            $depo = "(" . substr($aux, 4, strlen($aux)) . ")";

            $cond = ($depo != "()")
                ? ("AND ".$depo)
                : "";
        }

        //QUERY
        $sql = "SELECT depo.CodUbic AS almacen/*,  SUM(exis.Existen * prod.CostAct) AS total_b, SUM(exis.exunidad * (prod.CostAct/NULLIF(prod.CantEmpaq,0))) AS total_p
                */FROM SADEPO depo
                    INNER JOIN SAEXIS exis ON depo.CodUbic = exis.CodUbic
                    INNER JOIN SAPROD prod ON exis.CodProd = prod.CodProd
                    INNER JOIN SAPROD_02 prod02 ON exis.CodProd = prod02.CodProd
                WHERE (exis.existen > 0 OR exis.exunidad > 0) AND len(prod.marca) > 0 $cond
                GROUP BY depo.CodUbic ORDER BY depo.CodUbic ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        if ($depo != "()") {
            foreach ($alm AS $num)
                $sql->bindValue($i+=1, $num);
        }
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


     public function get_detalle_almacen($alm) {
        $i = 0;
        $cond = $depo = "";
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
       
        $sql="SELECT DISTINCT prod.marca AS instancia FROM SADEPO depo
                    INNER JOIN SAEXIS exis ON depo.CodUbic = exis.CodUbic
                    INNER JOIN SAPROD prod ON exis.CodProd = prod.CodProd
                    INNER JOIN SAPROD_02 prod02 ON exis.CodProd = prod02.CodProd
					inner join SAINSTA on SAINSTA.CodInst = prod.codinst
                WHERE (exis.existen > 0 OR exis.exunidad > 0) AND len(prod.marca) > 0 and depo.codubic = '$alm'
                GROUP BY prod.marca ";

              /*  $sql="SELECT DISTINCT prod.marca AS instancia FROM SADEPO depo
                INNER JOIN SAEXIS exis ON depo.CodUbic = exis.CodUbic
                INNER JOIN SAPROD prod ON exis.CodProd = prod.CodProd
            WHERE (exis.existen > 0 OR exis.exunidad > 0) AND len(prod.marca) > 0 and depo.codubic = '$alm' /*and Marca not in ('LONAS','MANA','LE BISCUIT - SNACK')*/
            /*GROUP BY prod.marca ";*/

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_detalle_almacen_producto($alm,$insta) {
        $i = 0;
        $cond = $depo = "";
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY

                $sql="SELECT  CantEmpaq AS display, CostAct AS costo,SUM(saexis.Existen) AS cantidad_b,SUM(saexis.exunidad) AS cantidad_p, SUM(saexis.Existen * saprod.CostAct) AS valor_b,SUM(saexis.exunidad * (saprod.CostAct/NULLIF(saprod.CantEmpaq,0))) AS valor_p
                FROM saprod INNER JOIN saexis ON saprod.codprod = saexis.codprod
                WHERE (saexis.existen > 0 OR saexis.exunidad > 0) AND len(marca) > 0 AND saexis.codubic IN ('$alm') AND marca LIKE '%$insta%'
                GROUP BY  CantEmpaq, CostAct";


               /* $sql="SELECT exis.CodUbic, SUM(exis.Existen) AS cantidad_b,SUM(exis.exunidad) AS cantidad_p, SUM((exis.Existen * prod.CostAct)) AS valor_b, SUM((exis.exunidad * (prod.CostAct/NULLIF(prod.CantEmpaq,0)))) AS valor_p
                FROM  SAEXIS exis INNER JOIN SAPROD prod ON exis.CodProd = prod.CodProd
                WHERE (exis.existen > 0 OR exis.exunidad > 0) AND marca LIKE '%$insta%' and exis.codubic = '$alm'
                GROUP BY exis.CodUbic ORDER BY exis.CodUbic ASC";*/

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }






    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_clientes_por_tipo($tipo = 0){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT codclie, descrip, id3, codvend, fechae, tipoid3, activo FROM SACLIE WHERE activo ='1' AND TipoID3 = ? AND codvend NOT IN ('99')";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tipo);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }



     public function get_n_documento($fechai,$fechaf,$tipo){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
          $sql = "SELECT [CodSucu]
                        ,[TipoFac]
                        ,[NumeroD]
                        ,[NroUnico]
                        ,[NroCtrol]
                        ,[ONumero]
                        ,[NumeroC]
                        ,[NumeroT]
                        ,[NumeroR]
                        ,[ID3]
                        ,[Monto]
                        ,[MtoTax]
                        ,[Fletes]
                        ,[TGravable]
                        ,[TExento]
                    FROM SAFACT where TipoFac='$tipo' and FechaE BETWEEN '$fechai' AND '$fechaf' and NumeroR IS NULL";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }



    public function get_tasa_dolar(){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        //$sql = "SELECT TOP(1) FechaE AS fechae, Tasa AS tasa FROM SACOMP WHERE Tasa IS NOT NULL ORDER BY FechaE DESC";
         $sql= "SELECT factor as tasa FROM SACONF WHERE CodSucu = 00000";
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_devoluciones_sin_motivo_Factura($tipodespacho) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $parameter = hash_equals('1', $tipodespacho)
            ? ', saclie.fechae as fecha_ini_clie, desp.numerod, ID_Correlativo as correl,notas1, notas2, observacion as motivo'
            : '';

        $relation = hash_equals('1', $tipodespacho)
            ? ' inner join APPWEB_DCONFISUR.dbo.Despachos_Det desp on desp.numerod = NumeroR '
            : '';

        $condition = hash_equals('0', $tipodespacho)
            ? " AND (numerod NOT IN (SELECT numerod FROM APPWEB_DCONFISUR.dbo.Despachos_Det) AND NumeroR NOT IN (SELECT numerod FROM APPWEB_DCONFISUR.dbo.Despachos_Det)) "
            : " AND (observacion IS NULL OR observacion = '') ";

        //QUERY
        $sql = "SELECT safact.codvend AS code_vendedor, safact.tipofac, safact.numerod, numeror, safact.fechae AS fecha_fact,
                       safact.codclie AS cod_clie, safact.descrip AS cliente, monto $parameter
                FROM SAFACT
                        INNER JOIN saclie ON safact.codclie = saclie.codclie
                        $relation
                WHERE safact.TipoFac = 'B' $condition
                ORDER BY fecha_fact DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);

    }

    public function get_devoluciones_sin_motivo_NotadeEntrega($tipodespacho) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $parameter = hash_equals('1', $tipodespacho)
            ? ', saclie.fechae AS fecha_ini_clie, nt.observacion AS motivo, ID_Correlativo, desp.numerod, notas1'
            : '';

        $relation = hash_equals('1', $tipodespacho)
            ? ' INNER JOIN APPWEB_DCONFISUR.dbo.Despachos_Det AS desp ON desp.numerod = numerof'
            : '';

        $condition = hash_equals('0', $tipodespacho)
            ? " AND (numerod NOT IN (SELECT numerod FROM APPWEB_DCONFISUR.dbo.Despachos_Det) AND numerof NOT IN (select numerod FROM APPWEB_DCONFISUR.dbo.Despachos_Det))"
            : " AND (nt.observacion IS NULL or nt.observacion = '')";

        //QUERY
        $sql = "SELECT nt.codvend AS code_vendedor, nt.tipofac, numerof AS numeror, nt.numerod, nt.fechae AS fecha_fact, nt.codclie AS cod_clie,
                       nt.rsocial AS cliente, total AS monto $parameter
                FROM SANOTA nt
                         INNER JOIN saclie ON nt.codclie = saclie.codclie
                         $relation
                WHERE nt.TipoFac = 'D' $condition
                ORDER BY fecha_fact DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }




  public function get_ventas_por_productos_fact($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT prod.Descrip as marca,
                       SUM(COALESCE((TotalItem/NULLIF(Tasai,0)) * (CASE WHEN itemfact.TipoFac = 'A' THEN 1 ELSE -1 END), 0)) as montod
                FROM SAFACT fact
                    INNER JOIN SAITEMFAC itemfact ON itemfact.NumeroD = fact.NumeroD
                    INNER JOIN SAPROD prod ON prod.CodProd = itemfact.CodItem
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fact.FechaE)) BETWEEN ? AND ? AND itemfact.tipofac IN ('A')
                  AND fact.NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))
                GROUP BY prod.Descrip
                ORDER BY montod DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }



      public function get_ventas_por_productos_nota($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        
            /*    $sql = "SELECT prod.Descrip as marca,
                       SUM(COALESCE(itemnota.total * (CASE WHEN itemnota.TipoFac = 'C' THEN 1 ELSE -1 END), 0)) AS montod
                FROM SANOTA nota
                    INNER JOIN SAITEMNOTA itemnota ON itemnota.numerod = nota.numerod
                    INNER JOIN SAPROD prod ON prod.CodProd = itemnota.coditem
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, itemnota.FechaE)) BETWEEN ? AND ? AND nota.tipofac IN ('C','D') AND numerof = '0'
                GROUP BY prod.Descrip
                ORDER BY montod DESC";*/

        $sql="SELECT prod.Descrip as marca,
                       SUM(COALESCE((TotalItem/NULLIF(Tasai,0)) * (CASE WHEN itemfact.TipoFac = 'C' THEN 1 ELSE -1 END), 0)) as montod
                FROM SAFACT fact
                    INNER JOIN SAITEMFAC itemfact ON itemfact.NumeroD = fact.NumeroD
                    INNER JOIN SAPROD prod ON prod.CodProd = itemfact.CodItem
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fact.FechaE)) BETWEEN ? AND ? AND fact.tipofac IN ('C')
                  AND fact.NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'C' AND x.NumeroR IS NOT NULL AND CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))
                GROUP BY prod.Descrip
                ORDER BY montod DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }







    public function get_ventas_por_marca_fact($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
       /* $sql = "SELECT marca,
                       SUM(COALESCE((TotalItem/NULLIF(Tasai,0)) * (CASE WHEN itemfact.TipoFac = 'A' THEN 1 ELSE -1 END), 0)) as montod
                FROM SAFACT fact
                    INNER JOIN SAITEMFAC itemfact ON itemfact.NumeroD = fact.NumeroD
                    INNER JOIN SAPROD prod ON prod.CodProd = itemfact.CodItem
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fact.FechaE)) BETWEEN ? AND ? AND fact.tipofac = 'A' 
                  AND fact.NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))
                GROUP BY marca
                ORDER BY montod DESC";*/
        $sql="SELECT
                SAITEMFAC.TipoFac AS tipo,
                (SELECT marca FROM SAPROD WHERE SAITEMFAC.coditem = SAPROD.CodProd) AS marca,
                SAITEMFAC.cantidad,
                (CASE SAITEMFAC.EsUnid WHEN 1 then 'PAQ' ELSE 'BULT' END) AS unid,
                (CASE SAITEMFAC.EsUnid WHEN 1 then cantidad ELSE cantidad*cantempaq END) AS paq,
                (CASE SAITEMFAC.EsUnid WHEN 1 then cantidad/cantempaq ELSE cantidad END) AS bul,
                SAITEMFAC.TotalItem as montod
                 FROM SAITEMFAC INNER JOIN saprod ON SAITEMFAC.coditem = saprod.codprod
                 INNER JOIN SAFACT ON SAITEMFAC.numerod = SAFACT.numerod AND SAITEMFAC.tipofac = SAFACT.tipofac WHERE
                 DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMFAC.FechaE)) between ? AND ? AND (SAITEMFAC.tipofac = 'A' OR SAITEMFAC.Tipofac = 'B') ORDER BY SAITEMFAC.fechae asc


";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ventas_por_marca_nota($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        
      /*  $sql = "SELECT marca,
                       SUM(COALESCE(itemnota.total * (CASE WHEN itemnota.TipoFac = 'C' THEN 1 ELSE -1 END), 0)) AS montod
                FROM SANOTA nota
                    INNER JOIN SAITEMNOTA itemnota ON itemnota.numerod = nota.numerod
                    INNER JOIN SAPROD prod ON prod.CodProd = itemnota.coditem
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, itemnota.FechaE)) BETWEEN ? AND ? AND nota.tipofac IN ('C','D') AND numerof = '0'
                GROUP BY marca
                ORDER BY montod DESC";*/

               /* $sql="SELECT marca,
                       SUM(COALESCE((itemfact.TotalItem/NULLIF(itemfact.Tasai,0)) * (CASE WHEN itemfact.TipoFac = 'C' THEN 1 ELSE -1 END), 0)) as montod
                FROM SAFACT fact
                    INNER JOIN SAITEMFAC itemfact ON itemfact.NumeroD = fact.NumeroD
                    INNER JOIN SAPROD prod ON prod.CodProd = itemfact.CodItem
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fact.FechaE)) BETWEEN  ? AND ? AND fact.tipofac ='C'  
                GROUP BY marca
                ORDER BY montod DESC";*/


                $sql="SELECT
                SAITEMNOTA.tipofac as tipo,
				SAITEMNOTA.esexento,
                (SELECT marca FROM SAPROD WHERE SAITEMNOTA.coditem = SAPROD.CodProd) AS marca,
                SAITEMNOTA.cantidad,
                (CASE SAITEMNOTA.esunidad WHEN 1 then 'PAQ' ELSE 'BULT' END) AS unid,
                (CASE SAITEMNOTA.esunidad WHEN 1 then cantidad ELSE cantidad*cantempaq END) AS paq,
                (CASE SAITEMNOTA.esunidad WHEN 1 then cantidad/cantempaq ELSE cantidad END) AS bul,
                (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.total ELSE SAITEMNOTA.total / 1.16 END) AS montod
                 FROM SAITEMNOTA INNER JOIN saprod ON SAITEMNOTA.coditem = saprod.codprod
                 INNER JOIN sanota ON saitemnota.numerod = sanota.numerod AND saitemnota.tipofac = sanota.tipofac WHERE
                 DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) between ? AND ?  AND (SAITEMNOTA.tipofac = 'C' OR SAITEMNOTA.Tipofac = 'D') AND  
                SANOTA.numerof =(SELECT numerof FROM sanota WHERE sanota.numerod = SAITEMNOTA.numerod AND sanota.tipofac = SAITEMNOTA.tipofac AND sanota.numerof = 0) ORDER BY SAITEMNOTA.fechae


";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }




    public function get_ventas_clientes_fact($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT clie.codclie, clie.Descrip,
                       SUM(COALESCE((TotalItem/NULLIF(Tasai,0)) * (CASE WHEN itemfact.TipoFac = 'A' THEN 1 ELSE -1 END), 0)) as montod
                FROM SAFACT fact
                         INNER JOIN SAITEMFAC itemfact ON itemfact.NumeroD = fact.NumeroD
                         INNER JOIN SAPROD prod ON prod.CodProd = itemfact.CodItem
                         INNER JOIN SACLIE clie ON clie.CodClie = fact.codclie
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fact.FechaE)) BETWEEN ? AND ? AND itemfact.tipofac IN ('A')
                  AND fact.NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))
                GROUP BY clie.codclie, clie.Descrip
                ORDER BY montod DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ventas_cliente_nota($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT clie.codclie, clie.Descrip,
                       SUM(COALESCE(itemnota.total * (CASE WHEN itemnota.TipoFac = 'C' THEN 1 ELSE -1 END), 0)) AS montod
                FROM SANOTA nota
                         INNER JOIN SAITEMNOTA itemnota ON itemnota.numerod = nota.numerod
                         INNER JOIN SAPROD prod ON prod.CodProd = itemnota.coditem
                         INNER JOIN SACLIE clie ON clie.CodClie = nota.codclie
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, itemnota.FechaE)) BETWEEN ? AND ? AND nota.tipofac IN ('C') AND numerof = '0'
                GROUP BY clie.codclie, clie.Descrip
                ORDER BY montod DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    ////////////////////////////////////////////// MASTER /////////////////////////////////////////////////////////////////

    public function get_master(){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
    
 
              $sql="DECLARE @fechai DATE
                    DECLARE @fechaf DATE
                    DECLARE @fecha_ini_mes DATE
                    set @fechai = GETDATE()
                    set @fechaf = GETDATE()
                    set @fecha_ini_mes = DATEADD(dd,-(DAY(GETDATE())-1),GETDATE())
                    select
                    count(case when TipoFac = 'C' then numerod end) Cant_Fact,
                    count(case when TipoFac = 'D' then numerod end) Cant_Devol,
                    CONVERT(varchar, (sum(case when TipoFac = 'C' then total end)), 1) Vendido,
                    CONVERT(varchar, (sum(case when TipoFac = 'D' then total when TipoFac = 'C' then 0 end) ), 1) Devoluciones,
                    CONVERT(varchar, (sum(case when TipoFac = 'C' then ISNULL(descuento,0)  when TipoFac = 'D' then ISNULL(descuento,0) *-1  end) ), 1) Descuento,
                    CONVERT(varchar, (sum(case when TipoFac = 'C' then total when TipoFac = 'D' then total * -1 end) -
                    sum(case when TipoFac = 'C' then ISNULL(descuento,0)  when TipoFac = 'D' then ISNULL(descuento,0) *-1  end)), 1) Total
                    from
                    SANOTA where TipoFac in ('C','D') and DATEADD(dd, 0, DATEDIFF(dd, 0, SANOTA.fechae)) BETWEEN @fecha_ini_mes and @fechaf

                    union

                    select
                    count(case when TipoFac = 'A' then numerod end) Cant_Fact,
                    count(case when TipoFac = 'B' then numerod end) Cant_Devol,
                    CONVERT(varchar, (sum(case when TipoFac = 'A' then monto/tasa end) ), 1) Vendido,
                    CONVERT(varchar, (sum(case when TipoFac = 'B' then monto/tasa when TipoFac = 'A' then 0 end) ), 1) Devoluciones,
                    CONVERT(varchar, (sum(case when TipoFac = 'A' then (Descto1 + Descto2)/tasa when TipoFac = 'B' then ((Descto1 + Descto2)/tasa)*-1  end) ), 1) Descuento,
                    CONVERT(varchar, (sum(case when TipoFac = 'A' then monto/tasa when TipoFac = 'B' then (monto/tasa) * -1 end) -
                    sum(case when TipoFac = 'A' then (Descto1 + Descto2)/tasa when TipoFac = 'B' then ((Descto1 + Descto2)/tasa)*-1  end) ), 1) Total
                    from SAFACT where TipoFac in ('A','B') and DATEADD(dd, 0, DATEDIFF(dd, 0, safact.fechae)) BETWEEN @fecha_ini_mes and @fechaf";


        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


////////////////////////////////////////////// VENTAS /////////////////////////////////////////////////////////////////

    public function get_total_ventas($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
    
 
                $sql="SELECT
                SAITEMNOTA.tipofac as tipo,
				SAITEMNOTA.esexento,
                (SELECT marca FROM SAPROD WHERE SAITEMNOTA.coditem = SAPROD.CodProd) AS marca,
                SAITEMNOTA.cantidad,
                (CASE SAITEMNOTA.esunidad WHEN 1 then 'PAQ' ELSE 'BULT' END) AS unid,
                (CASE SAITEMNOTA.esunidad WHEN 1 then cantidad ELSE cantidad*cantempaq END) AS paq,
                (CASE SAITEMNOTA.esunidad WHEN 1 then cantidad/cantempaq ELSE cantidad END) AS bul,
                (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.total ELSE SAITEMNOTA.total / 1.16 END) AS Vendido
                 FROM SAITEMNOTA INNER JOIN saprod ON SAITEMNOTA.coditem = saprod.codprod
                 INNER JOIN sanota ON saitemnota.numerod = sanota.numerod AND saitemnota.tipofac = sanota.tipofac WHERE
                DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) between ? AND ?  AND (SAITEMNOTA.tipofac = 'C' OR SAITEMNOTA.Tipofac = 'D') AND  
                SANOTA.numerof =(SELECT numerof FROM sanota WHERE sanota.numerod = SAITEMNOTA.numerod AND sanota.tipofac = SAITEMNOTA.tipofac AND sanota.numerof = 0) ORDER BY SAITEMNOTA.fechae";


        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }




     public function get_total_ventasbolivares($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY

                $sql="SELECT SAITEMFAC.TipoFac AS tipo,
                (SELECT marca FROM SAPROD WHERE SAITEMFAC.coditem = SAPROD.CodProd) AS marca,
                SAITEMFAC.cantidad,
                (CASE SAITEMFAC.EsUnid WHEN 1 then 'PAQ' ELSE 'BULT' END) AS unid,
                (CASE SAITEMFAC.EsUnid WHEN 1 then cantidad ELSE cantidad*cantempaq END) AS paq,
                (CASE SAITEMFAC.EsUnid WHEN 1 then cantidad/cantempaq ELSE cantidad END) AS bul,
                (SELECT tasa FROM SAFACT WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS factor,
                SAITEMFAC.TotalItem as Vendidobs
                 FROM SAITEMFAC INNER JOIN saprod ON SAITEMFAC.coditem = saprod.codprod
                 INNER JOIN SAFACT ON SAITEMFAC.numerod = SAFACT.numerod AND SAITEMFAC.tipofac = SAFACT.tipofac WHERE
                 DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMFAC.FechaE)) between ? AND ? AND (SAITEMFAC.tipofac = 'A' OR SAITEMFAC.Tipofac = 'B') ORDER BY SAITEMFAC.fechae asc";


        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
       // $sql->bindValue(3, $fechai);
       // $sql->bindValue(4, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }



    

/////////////////////////  detalles  ///////////////////////////////////////////////

public function get_bultos_detalles_fact($fechai, $fechaf, $mes) {
    $i = 0;
    //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
    //CUANDO ES APPWEB ES CONEXION.
    $conectar= parent::conexion2();
    parent::set_names();


            $sql="SELECT
YEAR(CAST(SAITEMFAC.FechaE AS DATETIME)) anio, MONTH(CAST(SAITEMFAC.FechaE AS DATETIME)) mes,
            SAITEMFAC.TipoFac AS tipo,
            SAITEMFAC.cantidad,
            (CASE SAITEMFAC.EsUnid WHEN 1 then 'PAQ' ELSE 'BULT' END) AS unid,
            (CASE SAITEMFAC.EsUnid WHEN 1 then cantidad/cantempaq ELSE cantidad END) AS bul,
            SAITEMFAC.fechae
             FROM SAITEMFAC INNER JOIN saprod ON SAITEMFAC.coditem = saprod.codprod
             INNER JOIN SAFACT ON SAITEMFAC.numerod = SAFACT.numerod AND SAITEMFAC.tipofac = SAFACT.tipofac WHERE
             DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMFAC.FechaE)) between ? AND ? AND MONTH(CAST(SAITEMFAC.FechaE AS DATETIME))=? AND (SAITEMFAC.tipofac = 'A' OR SAITEMFAC.Tipofac = 'B') ORDER BY SAITEMFAC.fechae asc
";

    //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
    $sql = $conectar->prepare($sql);
    $sql->bindValue($i+=1, $fechai);
    $sql->bindValue($i+=1, $fechaf);
    $sql->bindValue($i+=1, $mes);
    $sql->execute();
    return $sql->fetchAll(PDO::FETCH_ASSOC);
}


public function get_bultos_detalles_nota($fechai, $fechaf, $mes) {
    $i = 0;
    //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
    //CUANDO ES APPWEB ES CONEXION.
    $conectar= parent::conexion2();
    parent::set_names();


$sql="SELECT YEAR(CAST(SAITEMNOTA.FechaE AS DATETIME)) anio, MONTH(CAST(SAITEMNOTA.FechaE AS DATETIME)) mes,
            SAITEMNOTA.cantidad,
            SAITEMNOTA.TipoFac AS tipo,
            (CASE SAITEMNOTA.esunidad WHEN 1 then 'PAQ' ELSE 'BULT' END) AS unid,
            (CASE SAITEMNOTA.esunidad WHEN 1 then cantidad/cantempaq ELSE cantidad END) AS bul,
            SAITEMNOTA.fechae
             FROM SAITEMNOTA INNER JOIN saprod ON SAITEMNOTA.coditem = saprod.codprod
             INNER JOIN sanota ON saitemnota.numerod = sanota.numerod AND saitemnota.tipofac = sanota.tipofac WHERE
             DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) between ? AND ? AND (SAITEMNOTA.tipofac = 'C' OR SAITEMNOTA.Tipofac = 'D') AND  
            SANOTA.numerof =(SELECT numerof FROM sanota WHERE sanota.numerod = SAITEMNOTA.numerod AND sanota.tipofac = SAITEMNOTA.tipofac AND sanota.numerof = 0) AND MONTH(CAST(SAITEMNOTA.FechaE AS DATETIME))=? ORDER BY SAITEMNOTA.fechae";

    //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
    $sql = $conectar->prepare($sql);
    $sql->bindValue($i+=1, $fechai);
    $sql->bindValue($i+=1, $fechaf);
    $sql->bindValue($i+=1, $mes);
    $sql->execute();
    return $sql->fetchAll(PDO::FETCH_ASSOC);
}



public function get_ventas_detalle_fact($fechai, $fechaf, $mes) {
    $i = 0;
    //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
    //CUANDO ES APPWEB ES CONEXION.
    $conectar= parent::conexion2();
    parent::set_names();

    //QUERY

    $sql="SELECT YEAR(CAST(SAITEMFAC.FechaE AS DATETIME)) anio, MONTH(CAST(SAITEMFAC.FechaE AS DATETIME)) mes,
            SAITEMFAC.TipoFac AS tipo,
            (SELECT tasa FROM SAFACT WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS factor,
            SAITEMFAC.TotalItem as total
             FROM SAITEMFAC INNER JOIN saprod ON SAITEMFAC.coditem = saprod.codprod
             INNER JOIN SAFACT ON SAITEMFAC.numerod = SAFACT.numerod AND SAITEMFAC.tipofac = SAFACT.tipofac WHERE
             DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMFAC.FechaE)) between ? AND ? AND (SAITEMFAC.tipofac = 'A' OR SAITEMFAC.Tipofac = 'B') AND MONTH(CAST(SAITEMFAC.FechaE AS DATETIME))=? ORDER BY SAITEMFAC.fechae asc";

    //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
    $sql = $conectar->prepare($sql);
    $sql->bindValue($i+=1, $fechai);
    $sql->bindValue($i+=1, $fechaf);
    $sql->bindValue($i+=1, $mes);
    $sql->execute();
    return $sql->fetchAll(PDO::FETCH_ASSOC);
}

public function get_ventas_detalle_nota($fechai, $fechaf, $mes) {
    $i = 0;
    //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
    //CUANDO ES APPWEB ES CONEXION.
    $conectar= parent::conexion2();
    parent::set_names();

    //QUERY

    $sql="SELECT YEAR(CAST(sanota.fechae AS DATETIME)) anio, MONTH(CAST(sanota.fechae AS DATETIME)) mes,
            SAITEMNOTA.tipofac as tipo,
            SAITEMNOTA.esexento,
            (SELECT marca FROM SAPROD WHERE SAITEMNOTA.coditem = SAPROD.CodProd) AS marca,
            SAITEMNOTA.cantidad,
            SAITEMNOTA.esexento,
            (CASE SAITEMNOTA.esunidad WHEN 1 then 'PAQ' ELSE 'BULT' END) AS unid,
            (CASE SAITEMNOTA.esunidad WHEN 1 then cantidad ELSE cantidad*cantempaq END) AS paq,
            (CASE SAITEMNOTA.esunidad WHEN 1 then cantidad/cantempaq ELSE cantidad END) AS bul,
            (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.total ELSE SAITEMNOTA.total / 1.16 END) AS total
             FROM SAITEMNOTA INNER JOIN saprod ON SAITEMNOTA.coditem = saprod.codprod
             INNER JOIN sanota ON saitemnota.numerod = sanota.numerod AND saitemnota.tipofac = sanota.tipofac WHERE
            DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) between ? AND ?  AND (SAITEMNOTA.tipofac = 'C' OR SAITEMNOTA.Tipofac = 'D') AND  
            SANOTA.numerof =(SELECT numerof FROM sanota WHERE sanota.numerod = SAITEMNOTA.numerod AND sanota.tipofac = SAITEMNOTA.tipofac AND sanota.numerof = 0) and MONTH(CAST(sanota.fechae AS DATETIME))=? ORDER BY SAITEMNOTA.fechae

";

    //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
    $sql = $conectar->prepare($sql);
    $sql->bindValue($i+=1, $fechai);
    $sql->bindValue($i+=1, $fechaf);
    $sql->bindValue($i+=1, $mes);
    $sql->execute();
    return $sql->fetchAll(PDO::FETCH_ASSOC);
}




}

