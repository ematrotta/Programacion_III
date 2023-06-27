<?php

class ModificarVenta{
    static function ModificarVenta($nroPedido,$mailUsuario,$sabor,$tipo,$cantidad,$file)
    {
        $rtn = true;
        $arrayVentas = Venta::ReadJSON("Ventas.json");
        foreach($arrayVentas as $venta)
        {

            if($venta->_nroPedido == $nroPedido)
            {

                if(!empty($mailUsuario))
                {
                    $usuario = Usuario::ObtenerUsuarioByMail($mailUsuario,"Usuarios.json");
                    if($usuario != false)
                    {
                        $venta->_idUsuario = $usuario->_id;

                    }
                    else{
                        echo "No se encontró el usuario";
                        $rtn = false;
                    }
                    

                }
                if(!empty($sabor) && !empty($sabor))
                {
                    $pizza = Pizza::ObtenerPizza($tipo,$sabor,"Pizza.json");
                    if($pizza != false)
                    {
                        $venta->_idPizza = $pizza->_id;

                    }
                    else{
                        echo "No se encontró el tipo de pizza";
                        $rtn = false;
                    }
                    
                }
                if(!empty($cantidad))
                {
                    if($cantidad>0)
                    {
                        $venta->_cantidadPizza = $cantidad;

                    }
                    else{
                        echo "La cantidad debe ser mayor a 0";
                        $rtn = false;
                    }
                    
                }

                // Sobreescribo el archivo anterior
                $file = fopen($file,"w");
                $jsonString = json_encode($arrayVentas,JSON_PRETTY_PRINT);
                fwrite($file,$jsonString);
                fclose($file);
                break;

            }
        }
        return $rtn;

    }

}


?>