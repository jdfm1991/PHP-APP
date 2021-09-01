
<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Principal extends Conectar{

    public function getDocumentosSinDespachar(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT *
                FROM safact AS SA INNER JOIN SAVEND AS VEND ON VEND.CodVend = SA.CodVend
                WHERE SA.NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det)
                  AND SA.TipoFac = 'A'
                  AND (SA.NumeroR IS NULL OR SA.NumeroR IN (SELECT x.NumeroD FROM SAFACT AS x WHERE cast(x.Monto AS INT)<cast(SA.Monto AS INT) AND X.TipoFac = 'B'
                  AND x.NumeroD=SA.NumeroR))  AND SA.NumeroD NOT IN (SELECT numerof FROM sanota) ORDER BY SA.NumeroD";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}

