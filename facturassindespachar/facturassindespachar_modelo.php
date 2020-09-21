
<?php
//LLAMAMOS A LA CONEXION.
require_once("../acceso/conexion.php");

class FacturaSinDes extends Conectar{
    public function getFacturas($tipo, $fechai, $fechaf, $convend, $verDespachadas)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        if($verDespachadas) { //ver las facturas despachadas
            $valores_adicionales = '
                (SELECT fechad FROM APPWEBAJ.dbo.Despachos INNER JOIN APPWEBAJ.dbo.Despachos_Det ON Despachos.Correlativo = Despachos_Det.ID_Correlativo 
                    WHERE Despachos_Det.Numerod = sa.numerod) AS fechad,
                (SELECT Tiempo_Estimado_Despacho FROM savend_02 WHERE savend_02.codvend = sa.codvend) AS testimado,';
            $condicion_1 = '
                SA.NumeroD IN (SELECT Numerod FROM APPWEBAJ.dbo.Despachos_Det INNER JOIN APPWEBAJ.dbo.Despachos ON Despachos.Correlativo = Despachos_Det.ID_Correlativo
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fechad)) BETWEEN ? AND ?)';
            $condicion_2 = "";
        } else { //sino, NO ver las facturas despachadas
            $valores_adicionales = "";
            $condicion_1 = '
                SA.NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det)
                AND DATEADD(dd, 0, DATEDIFF(dd, 0, SA.FechaE)) BETWEEN ? AND ?';
            $condicion_2 = ' AND SA.NumeroD NOT IN (SELECT numerof FROM sanota)';
        }
        $codven = (!hash_equals("-", $convend)) ? "AND SA.codvend = ?": "";
        $clase = (!hash_equals("-", $tipo)) ? "AND VEND.Clase = ?": "";

        $sql = "SELECT *, $valores_adicionales
                (SELECT sum(cantidad) FROM saitemfac WHERE saitemfac.numerod = SA.numerod AND saitemfac.tipofac = ? AND EsUnid = ?) AS Bult,
                (SELECT sum(cantidad) FROM saitemfac WHERE saitemfac.numerod = SA.numerod AND saitemfac.tipofac = ? AND EsUnid = ?) AS Paq
                FROM safact AS SA INNER JOIN SAVEND AS VEND ON VEND.CodVend = SA.CodVend
                WHERE $condicion_1
                AND SA.TipoFac = ? $codven $clase
                AND (SA.NumeroR IS NULL OR SA.NumeroR IN (SELECT x.NumeroD FROM SAFACT AS x WHERE cast(x.Monto AS INT)<cast(SA.Monto AS INT) AND X.TipoFac = ?
                AND x.NumeroD=SA.NumeroR)) $condicion_2 ORDER BY SA.NumeroD";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, 'A');
        $sql->bindValue($i+=1, '0');
        $sql->bindValue($i+=1, 'A');
        $sql->bindValue($i+=1, '1');
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, 'A');
        if(!hash_equals("-", $convend)) {
            $sql->bindValue($i+=1, $convend);
        }
        if(!hash_equals("-", $tipo)) {
            $sql->bindValue($i += 1, $tipo);
        }
        $sql->bindValue($i+=1, 'B');
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCanales(){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $sql= "SELECT DISTINCT Clase FROM SAVEND WHERE Clase IS NOT NULL AND LEN(Clase) > 1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_cabecera_factura_por_id($numerod, $tipofac){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $sql= "SELECT sa.numerod, sa.codvend AS vendedor, sa.codclie AS codcliente, sa.descrip AS cliente, sa.fechae AS fechaemi, sa.mtototal, sa.monto, sa.descto1, sa.mtotax, codusua, sataxvta.CodTaxs, sataxvta.MtoTax AS tax
                FROM safact AS sa
                    LEFT JOIN saclie ON sa.codclie = saclie.codclie
                    LEFT JOIN sataxvta ON sa.numerod = sataxvta.numerod
                WHERE sa.numerod = ? AND sa.tipofac = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $numerod);
        $sql->bindValue(2, $tipofac);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}