<?php

class Usuario{
    public int $_id;
    public string $_mail;

    public function __construct($mail,$id) {
        $this->_mail = $mail;
        $this->_id = $id;
    }

    static function ReadJSON($file)
    {
        $lista = array();
        // Verifico que exista el archivo JSON
        if(file_exists($file)==true)
        {
            $jsonString = file_get_contents($file);
            if($jsonString != false)
            {
                $objetosGuardados = json_decode($jsonString,false);
                foreach($objetosGuardados as $objeto)
                {
                    $objetoGuardado = new Usuario($objeto->_mail,intval($objeto->_id));
                    array_push($lista,$objetoGuardado);
                }
            }
        }
        
        return $lista;
    }

    function SaveJSON($file)
    {
        // Leo usuarios guardados en el archivo
        $objetosGuardados = Usuario::ReadJSON($file);
        array_push($objetosGuardados,$this);
        $file = fopen($file,"w");
        $jsonString = json_encode($objetosGuardados,JSON_PRETTY_PRINT);
        fwrite($file,$jsonString);
        fclose($file);
    
    }

    static function ObtenerId($file)
    {

        $arrayJson = Usuario::ReadJSON($file);
        $arrayIds = array();
        $rtn = random_int(1,10000);
        if(count($arrayJson)>0)
        {
            foreach($arrayJson as $objeto)
            {
                array_push($arrayIds,intval($objeto->_id));
            }
            $rtn = max($arrayIds)+1;
        }

        return $rtn;
    }

    static function ObtenerUsuarioById($idUsuario,$file)
    {
        $rtn = false;
        $arrayJson = Usuario::ReadJSON($file);
        foreach($arrayJson as $objeto)
        {
            if($objeto->_id == $idUsuario)
            {
                $rtn = $objeto;
                break;

            }
        }

        return $rtn;

    }
    static function ObtenerUsuarioByMail($mail,$file)
    {
        $rtn = false;
        $arrayJson = Usuario::ReadJSON($file);
        foreach($arrayJson as $objeto)
        {
            if($objeto->_mail == $mail)
            {
                $rtn = $objeto;
                break;

            }
        }

        return $rtn;

    }



}
?>