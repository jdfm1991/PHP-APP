<?php
require_once("../../config/conexion.php");

class Download extends Conectar{

    public function subsidiaryname(){

        $conectar= parent::conexion2();
        parent::set_names();
        
         //QUERY
 
             $sql= ("SELECT Descrip  FROM SACONF");
 
         //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
         $sql = $conectar->prepare($sql);
         $sql->execute();
         
         return ($sql->fetch(PDO::FETCH_ASSOC)['Descrip']) ;
     }
    

    public function substamps(){

        $conectar= parent::conexion2();
        parent::set_names();
        
        //QUERY

        $sql= ("SELECT DISTINCT sainsta.CodInst, sainsta.Descrip
                    FROM sainsta 
                    INNER JOIN saprod ON saprod.CodInst = sainsta.CodInst
                    INNER JOIN saexis ON saexis.CodProd = saprod.CodProd 
                WHERE (saexis.codubic = '01') ");

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        
        return $result ;
    }

    public function searchcontent($query){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();
    
        //QUERY
    
            $sql="$query";
    
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    
    }

    public function updateDataGeneral($idCommodity,$nombre_img){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();
    
        //QUERY
    
            $sql="UPDATE SAPROD_02 SET ImagenC = ? WHERE CodProd  = ?";
    
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $nombre_img);
        $sql->bindValue(2, $idCommodity);

        return $sql->execute();
    
    }



}
