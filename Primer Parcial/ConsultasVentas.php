<?php

    include_once "./Venta.php";

    function ConsultasVentas($tipoListado,$fechaInicio,$fechaFinal,$datoAdicional)
    {
        $rtn = "";
        $ventas = null;

        if(isset($tipoListado) && isset($datoAdicional))
        {
            $tipoListado = strtolower($tipoListado);
            $datoAdicional = strtolower($datoAdicional);
            if(isset($fechaInicio) && !empty($fechaInicio))
            {
                $fechaInicio = DateTime::createFromFormat("d/m/Y",$fechaInicio)->setTime(0,0,0);
            }
            else{
                // $fechaActual = strtotime("yesterday");
                // $fechaInicio = date("d-m-Y",$fechaActual);
                $fechaInicio = (new DateTime("yesterday"))->setTime(0,0,0);
            }
            
            if(isset($fechaFinal) && !empty($fechaFinal))
            {
                $fechaFinal = DateTime::createFromFormat("d/m/Y",$fechaFinal)->setTime(0,0,0);
            }
            else{
                $fechaFinal = (new DateTime())->setTime(0,0,0);
            }

            switch($tipoListado)
            {
                case "a":
                    $ventas = Venta::ObtenerVentasEntreFechas($fechaInicio,$fechaFinal,"Ventas.json");
                    $contadorDeVentas = 0;
                    if(count($ventas)>0)
                    {
                        foreach($ventas as $venta)
                        {
                            $contadorDeVentas += $venta->_cantidad;
    
                        }
                        echo "La cantidad de hamburguesas vendidas el rango de fechas indicado es: ".$contadorDeVentas;

                    }else{
                        $rtn = "No hay ventas en el rango de fechas indicado";
                    }

                    break;
                case "b":
                    $ventas = Venta::ObtenerVentasEntreFechas($fechaInicio,$fechaFinal,"Ventas.json");
                    if(count($ventas)>0)
                    {
                        $retornoSort = Venta::SortByName($ventas);
                        if($retornoSort == true)
                        {
                            echo "Listado de ventas entre fechas ordenados por nombre\n";
                            foreach($ventas as $venta)
                            {
                                echo $venta->Mostrar();
                            }
                        }
                        else{
                            echo "No se pudo ordenar correctamente el listado de ventas";
                        }
                    }
                    else{
                        $rtn = "No hay ventas en el rango de fechas indicado";
                    }
                    break;
                case "c":
                    $ventas = Venta::ObtenerVentasPorUsuario($datoAdicional,"Ventas.json");
                    if(count($ventas)>0)
                    {
                        echo "Listado de ventas del usuario ".$datoAdicional;
                        foreach($ventas as $venta)
                        {
                            
                            echo $venta->Mostrar();
    
                        }

                    }else{
                        $rtn = "No se registran ventas";
                    }
                    break;
                case "d":
                    $ventas = Venta::ObtenerVentasPorTipo($datoAdicional,"Ventas.json");
                    if(count($ventas)>0)
                    {
                        echo "Listado de ventas por tipo";
                        foreach($ventas as $venta)
                        {
                            echo $venta->Mostrar();
                        }

                    }else{
                        $rtn = "No se registran ventas";
                    }
                    break;
                case "e":
                    $ventas = Venta::ObtenerVentasPorAderezo("ketchup","Ventas.json");
                    if(count($ventas)>0)
                    {
                        echo "Listado de ventas por aderezo Ketchup";
                        foreach($ventas as $venta)
                        {
                            echo $venta->Mostrar();
                        }

                    }else{
                        $rtn = "No se registran ventas";
                    }
                    break;
                default:
                    $rtn = "No se ingresó el tipo de listado correcto. Se debe seleccionar:\na- La cantidad de Hamburguesas vendidas en un día en particular, si no se pasa fecha, se muestran sólo las
                    del día de ayer.\n
                    b- El listado de ventas entre dos fechas ordenado por nombre.\n
                    c- El listado de ventas de un usuario ingresado.\n
                    d- El listado de ventas de un tipo ingresado.\n
                    e- Listado de ventas de aderezo “Ketchup”";
                    break;
            }

            


        }
        return $rtn;

    }


?>