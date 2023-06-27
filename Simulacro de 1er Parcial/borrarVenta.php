<?php

include_once "./Venta.php";

class BorrarVenta{
    static function BorrarVenta($nroPedido,$file)
    {
        $rtn = false;
        $arrayVentas = Venta::ReadJSON("Ventas.json");
        foreach($arrayVentas as $venta)
        {
            if($venta->_nroPedido == $nroPedido)
            {
                // Cambio de lugar la imagen
                $venta->MoverImagen("ImagenesDeLaVenta","BACKUPVENTAS");
                // Elimino el elemento del array
                array_splice($arrayVentas,array_search($venta,$arrayVentas,true));

                // Sobreescribo el archivo anterior
                $file = fopen($file,"w");
                $jsonString = json_encode($arrayVentas,JSON_PRETTY_PRINT);
                fwrite($file,$jsonString);
                fclose($file);
                $rtn = true;
                break;

            }
        }
        return $rtn;

    }

}

?>