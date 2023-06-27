<?php

class PizzaCarga{
    static function CargarNuevaPizza($tipo,$cantidad,$precio,$sabor,$archivo = null)
    {
        $pizza = new Pizza($sabor,$precio,$tipo,$cantidad,Pizza::ObtenerId("Pizza.json"));
        if(Pizza::ObtenerPizza($tipo,$sabor,"Pizza.json") != false)
        {
            $pizza->Modificar("Pizza.json");

        }
        else{

            $pizza->SaveJSON("Pizza.json");
            if($archivo != null)
            {
                $pizza->GuardarImagen("ImagenesDePizzas",$tipo."_".$sabor,$archivo);

            } 

        }

        
        
    }
}


?>