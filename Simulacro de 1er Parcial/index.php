



<?php

include_once "./PizzaCarga.php";
include_once "./PizzaConsultar.php";
include_once "./AltaVenta.php";
include_once "./ModificarVenta.php";
include_once "./borrarVenta.php";

    switch($_SERVER["REQUEST_METHOD"])
    {
        case "POST":

            if(isset($_POST["tipo"]) && isset($_POST["sabor"]))
            {

                $tipo = strtolower($_POST["tipo"]);
                $sabor = strtolower($_POST["sabor"]);

                if(!empty($tipo) && ($tipo == Pizza::MOLDE || $tipo == Pizza::PIEDRA) && !empty($sabor))
                {
                    if(isset($_POST["cantidad"]) && isset($_FILES["file"]))
                    {
                        $cantidad = intval($_POST["cantidad"]);
                        $file = $_FILES["file"];
        
        
                        if(!empty($cantidad) && !empty($file))
                        {
        
                            // Parte 4: Dar de alta una pizza por POST
        
                            if(isset($_POST["precio"]) && !empty($_POST["precio"]))
                            {
                                $precio = floatval($_POST["precio"]);
                                $pizzaNueva = PizzaCarga::CargarNuevaPizza($tipo,$cantidad,$precio,$sabor,$file);
                                echo "Carga correcta";
                            }
                            else{
        
                                // Parte 2: Dar de alta una venta
        
                                if(isset($_POST["mail"]))
                                {
                                    $mail = strtolower($_POST["mail"]);
            
                                    if(!empty($mail) && filter_var($mail,FILTER_VALIDATE_EMAIL) && !empty($file))
                                    {
                                        $nuevaVenta = AltaVenta::AltaVenta($mail,$tipo,$sabor,$cantidad,$file);
                                        if($nuevaVenta != false)
                                        {
        
                                            $nuevaVenta->GuardarImagen("ImagenesDeLaVenta","image_".$tipo."_".$sabor."_".substr($mail,0,stripos($mail, '@'))."_".$nuevaVenta->_fechaCompra->format("d_m_Y H_i"),$file);
                                        }
                                        else{
                                            echo "No se pudo realizar el alta de la venta";
                                        }
                        
                                    }
                
                                }
            
                            }
            
            
                        }
    
                    }
                    else{
                        // Parte 1: Consultar si hay pizza o no por POST
    
                        if(PizzaConsultar::VerificarPizza($sabor,$tipo))
                        {
                            echo "Hay";
                        }
                        else{
                            echo "No hay";
                        }
                        
                    }
                }
                else{
                    echo "El tipo de pizza o sabor son erroneos";
                }
            }
            break;
        case "GET":
            // Parte 3: Listado de ventas

            if(isset($_GET["listado"]) && !empty($_GET["listado"])){
                $tipoListado = strval($_GET["listado"]);
                switch($tipoListado)
                {
                    case "a":
                        echo "La cantidad de pizzas vendidas al momento es: ".strval(Venta::ObtenerCantidadPizzasVendidas());
                        break;
                    case "b":
                        if(isset($_GET["fechaInicio"]) && isset($_GET["fechaFin"]) && !empty($_GET["fechaInicio"]) && !empty($_GET["fechaFin"])){



                            $fechaInicio = DateTime::createFromFormat("d/m/Y",$_GET["fechaInicio"])->setTime(0,0,0);
                            $fechaFin = DateTime::createFromFormat("d/m/Y",$_GET["fechaFin"])->setTime(0,0,0);
                            $arrayVentas = Venta::ObtenerVentasEntreFechas($fechaInicio,$fechaFin);

                            Venta::OrdenarVentasPorSabor($arrayVentas);
                            
                            echo "VENTAS POR RANGO DE FECHAS";
                            if(count($arrayVentas)>0){
                                foreach($arrayVentas as $venta)
                                {
                                    echo $venta->Mostrar();
                                    echo "<br>";
                                }

                            }
                            else{
                                echo "No se registran para ese rango de fechas";
                            }

                        }
                        else{
                            echo "Debe colocar los campos de Fecha Inicio y Fecha fin con el formato d/m/Y";
                        }

                        break;
                    case "c":
                        if(isset($_GET["mail"]) && !empty($_GET["mail"])){

                            $mailUsuario = $_GET["mail"];
                            $arrayVentasPorMail = Venta::ObtenerVentasByMailUsuario($mailUsuario);
                            echo "VENTAS POR MAIL DE USUARIO";
                            if($arrayVentasPorMail!= false && count($arrayVentasPorMail)>0){
                                foreach($arrayVentasPorMail as $venta)
                                {
                                    echo $venta->Mostrar();
                                    echo "<br>";
                                }

                            }
                            else{
                                echo "No se registran ventas para ese usuario";
                            }

                        }
                        else{
                            echo "Debe colocar el dato de mail";
                        }
                        break;
                    case "d":
                        if(isset($_GET["sabor"]) && !empty($_GET["sabor"])){

                            $sabor = $_GET["sabor"];
                            $arrayVentasPorSabor = Venta::ObtenerVentasBySabor($sabor);
                            echo "VENTAS POR SABOR";
                            if(count($arrayVentasPorSabor)>0){
                                foreach($arrayVentasPorSabor as $venta)
                                {
                                    echo $venta->Mostrar();
                                    echo "<br>";
                                }

                            }
                            else{
                                echo "No se registran ventas para ese sabor";
                            }

                        }
                        else{
                            echo "Debe colocar el dato de sabor";
                        }
                        break;
                    default:
                        echo "Debe colocar:";
                        echo "a- la cantidad de pizzas vendidas";
                        echo "b- el listado de ventas entre dos fechas ordenado por sabor.";
                        echo "c- el listado de ventas de un usuario ingresado";
                        echo "d- el listado de ventas de un sabor ingresado";

                        break;
                }

            }else
            {
                // Parte 1: Dar de alta pizza por GET

                if(isset($_GET["tipo"]) && isset($_GET["sabor"]) && !empty($_GET["tipo"]) && ($_GET["tipo"] == Pizza::MOLDE || $_GET["tipo"] == Pizza::PIEDRA) && !empty($_GET["sabor"]) && isset($_GET["cantidad"]) && !empty($_GET["cantidad"]) && isset($_GET["precio"]) && !empty($_GET["precio"]))
                {
                    $tipo = strtolower($_GET["tipo"]);
                    $sabor = strtolower($_GET["sabor"]);
                    $cantidad = intval($_GET["cantidad"]);
                    $precio = floatval($_GET["precio"]);
                    $pizzaNueva = PizzaCarga::CargarNuevaPizza($tipo,$cantidad,$precio,$sabor);
                    echo "Carga correcta";

                }
                else{
                    echo "Alguno de los datos es erroneo";
                }
            }
        case "PUT":
            if(isset($_GET["nroPedido"]) && isset($_GET["mailUsuario"]) && isset($_GET["sabor"]) && isset($_GET["tipo"]) && isset($_GET["cantidad"]))
            {
                if(ModificarVenta::ModificarVenta(intval($_GET["nroPedido"]),$_GET["mailUsuario"],$_GET["sabor"],$_GET["tipo"],intval($_GET["cantidad"]),"Ventas.json"))
                {
                    echo "Venta Modificada con exito";
                }
                else{
                    echo "Fallo en la modificación";
                }

            }

            break;

        case "DELETE":
            if(isset($_GET["nroPedido"]))
            {
                if(BorrarVenta::BorrarVenta(intval($_GET["nroPedido"]),"Ventas.json"))
                {
                    echo "Venta eliminada con exito";
                }
                else{
                    echo "Fallo no se pudo eliminar";
                }

            }
            
            break;

        


    }


?>
