<?php
    include_once "./Hamburguesa.php";

    function HamburguesaConsultar($nombre,$tipo)
    {
        
        if(isset($nombre) && isset($tipo) && !empty($tipo) && !empty($nombre))
        {
            $rtn = "No hay";
            $tipo = strtolower($tipo);
            $nombre = strtolower($nombre);
            if(Hamburguesa::ObtenerHamburguesa($tipo,$nombre) != false)
            {
                $rtn = "Hay";
            }
        }
        else{
            $rtn = "Error en alguno de los datis ingresados";

        }


        return $rtn;

    }

?>