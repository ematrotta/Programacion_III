<?php

include_once "./Usuario.php";
include_once "./Pizza.php";
include_once "./Venta.php";

class AltaVenta{

    static function AltaVenta($mail,$tipo,$sabor,$cantidad)
    {
        $rtn = false;
        $pizza = Pizza::ObtenerPizza($tipo,$sabor,"Pizza.json");
        $usuario = Usuario::ObtenerUsuarioByMail($mail,"Usuarios.json");
        $nuevaVenta = null;
        if($pizza != false)
        {
            if($pizza->RestarStock($cantidad,"Pizza.json"))
            {
                // Si el usuario no existe, lo creo
                if($usuario == false)
                {
                    $usuario = new Usuario($mail,Usuario::ObtenerId("Usuarios.json"));
                    $usuario->SaveJSON("Usuarios.json");
                }
                echo "Se restó stock";

                $nuevaVenta = new Venta(new DateTime(),Venta::ObtenerId("Ventas.json"),Venta::ObtenerNroPedido("Ventas.json"),$usuario->_id,$pizza->_id,$cantidad);
                $nuevaVenta->SaveJSON("Ventas.json");
                $rtn = $nuevaVenta;
            }
            else{
                echo "El stock es menor a la cantidad solicitada";
            }
            
        }
        else{
            echo "No se encuentra la pizza solicitada<br>";
        }
        return $rtn;
    }


}


?>