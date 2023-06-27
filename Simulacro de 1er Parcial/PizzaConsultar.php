<?php


include_once "./Pizza.php";

class PizzaConsultar{

    static function VerificarPizza($sabor,$tipo)
    {
        return Pizza::ObtenerPizza($tipo,$sabor,"Pizza.json");
    }

}
?>