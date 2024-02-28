<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class InventarioFinal extends Conectar
{
    public function getproductos($fechai, $fechaf, $alm=array())
    {
        $i = 0;
        $cond = $depo = "";
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();


                $calm=count($alm);
                 
        if ($calm=='1'){
            $cond="and SAEXIS.CodUbic='01'";
            $cond1="SAEXIS.CodUbic='01'";
        }else if ($calm=='2'){
            $cond="and (SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01')";
            $cond1="(SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01')";
        }else if ($calm=='3'){
            $cond="and (SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01')";
            $cond1="(SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01')";
        }else if ($calm=='4'){
            $cond="and (SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01')";
            $cond1="(SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01')";
        }

        $sql = "SELECT DISTINCT SAEXIS.CodProd,Descrip
                FROM SAEXIS inner join  SAPROD 
                on SAEXIS.CodProd = SAPROD.CodProd inner join
                SAITEMFAC on SAITEMFAC.CodItem = SAEXIS.CodProd
                where  SAPROD.activo='1' and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf' ".$cond." ";

          //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }


     public function getdata($fechai, $fechaf, $alm=array(), $codprod)
    {
        $i = 0;
        $cond = $depo = "";
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();


                $calm=count($alm);
        if ($calm=='1'){
            $cond="and SAEXIS.CodUbic='01'";
            $cond1="SAEXIS.CodUbic='01'";
        }else if ($calm=='2'){
            $cond="and (SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01')";
            $cond1="(SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01')";
        }else if ($calm=='3'){
            $cond="and (SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01')";
            $cond1="(SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01')";
        }else if ($calm=='4'){
            $cond="and (SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01')";
            $cond1="(SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01' or SAEXIS.CodUbic='01')";
        }

        $sql = "SELECT SAPROD.CantEmpaq, SAEXIS.CodProd, SAEXIS.Existen, SAEXIS.ExUnidad ,SAITEMFAC.Costo, SAITEMFAC.EsUnid,SAPROD.CantEmpaq, SAITEMFAC.Costo,SAITEMFAC.ExistAnt, SAITEMFAC.ExistAntU,SAITEMFAC.Cantidad
                FROM SAEXIS inner join  SAPROD 
                on SAEXIS.CodProd = SAPROD.CodProd inner join
                SAITEMFAC on SAITEMFAC.CodItem = SAEXIS.CodProd
                where SAITEMFAC.CodItem='$codprod' and SAPROD.activo='1' and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf' ".$cond." order by FechaE desc";
          //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }


}
