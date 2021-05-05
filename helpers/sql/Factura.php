<?php


class Factura extends Conectar {

    public static function getHeaderById($numerod)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT numerod, fechae, codusua, codvend, descrip, Tasa AS tasa,
                       monto AS subtotal, Descto1 AS descuento,
                       TExento AS excento, MtoTax AS impuesto,
                       COALESCE((MtoTax*100) / NULLIF(TGravable, 0), 0) AS iva,
                       TGravable AS base_imponible, MtoTotal as total
                FROM safact WHERE numerod = ?";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue(1, $numerod);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getDetailById($numerod, $tipo)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT nrolinea, tipofac, coditem, codvend, descrip1 AS descrip, cantidad, esunid,
                    precio, descto AS descuento, totalitem as total, COALESCE(tasai, 0) AS tasa,
                    (COALESCE(CASE WHEN esunid = 0 THEN (saprod.tara) ELSE (saprod.tara / saprod.cantempaq) END, 0) * saitemfac.cantidad) AS peso,
                    (CASE WHEN esunid = 0 THEN 'BUL' ELSE 'PAQ' END) AS tipo
                FROM saitemfac INNER JOIN saprod ON codprod = saitemfac.coditem
                WHERE numerod = ? AND tipofac = ? ORDER BY saitemfac.nrolinea";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue(1, $numerod);
        $result->bindValue(2, $tipo);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}