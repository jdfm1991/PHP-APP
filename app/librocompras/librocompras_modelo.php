
<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class LibroCompra extends Conectar{

    public function getLibroPorFecha($fechai, $fechaf)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql= "SELECT FechaE as fechacompra, CodProv as id3ex, Descrip as descripex, TipoCom as tipodoc, NumeroR as nroretencion, NumeroD as numerodoc, NroCtrol as nroctrol, MtoTotal as totalcompraconiva, TExento as mtoexento, TGravable as totalcompra,MtoTax as monto_iva from SACOMP  where DATEADD(dd, 0, DATEDIFF(dd, 0, SACOMP.FechaE)) between '$fechai' AND '$fechaf'  AND (SACOMP.TipoCom = 'J' OR SACOMP.TipoCom = 'K') ORDER BY SACOMP.fechae asc";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        /*$sql->bindValue($i+=1,$fechai);
        $sql->bindValue($i+=1,$fechaf);*/
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }
}

