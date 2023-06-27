<?php  
    include_once "./Hamburguesa.php";
    include_once "./Venta.php";
    include_once "./Cupon.php";
    
    function AltaVenta($nombre,$mailUsuario,$tipo,$aderezo,int $cantidad,$file = null,$nroCupon = null)
    {
        $rtn = "";
        $nombre = strtolower($nombre);
        $mailUsuario = strtolower($mailUsuario);
        $aderezo = strtolower($aderezo);
        $tipo = strtolower($tipo);

        if(isset($aderezo) && isset($tipo) && isset($cantidad) && ($aderezo == Hamburguesa::ADEREZO_KETCHUP || $aderezo == Hamburguesa::ADEREZO_MOSTAZA ||
         $aderezo == Hamburguesa::ADEREZO_MAYONESA) && ($tipo == Hamburguesa::TIPO_DOBLE || $tipo == Hamburguesa::TIPO_SIMPLE) && $cantidad>0)
        {

            $hamburguesa = Hamburguesa::ObtenerHamburguesa($tipo,$nombre);
            if($hamburguesa != false)
            {
                if($hamburguesa->ModificarStock("-",$cantidad,"Hamburguesas.json"))
                {
                    $nuevaVenta = new Venta($nombre,$mailUsuario,$tipo,$aderezo,new DateTime('now',new DateTimeZone("America/Argentina/Buenos_Aires")),$cantidad,Venta::ObtenerNroPedido("Ventas.json"),Venta::ObtenerId("Ventas.json"));
                    
                    // Si se ingresó un cupón
                    if($nroCupon != null && $nroCupon != "")
                    {
                        $cupon = Cupon::obteneraById($nroCupon,"Cupones.json");
                        if($cupon != false && $cupon->UsarCupon())
                        {

                            $resultadoDescuento = $nuevaVenta->AplicarDescuento($nuevaVenta->_importeFinal*$cupon->_porcentaje);
                            if($resultadoDescuento)
                            {
                                echo "Se aplico el descuento correctamente";
                            }

                        }
                        else{
                            echo "No se pudo utilizar el cupón. Asegurese de no haberlo usado antes o que esté en plazo";
                        }
                    }
                    $nuevaVenta->SaveJSON("Ventas.json");
                    $nuevaVenta->GuardarImagen("/ImagenesDeLaVenta/2023",$nuevaVenta->_tipo."_".$nuevaVenta->_nombre."_".substr($nuevaVenta->_mail,0,stripos($nuevaVenta->_mail, '@'))."_".$nuevaVenta->_fecha->format("d_m_Y H_i"),$file);
                    $rtn = "Venta realizada con exito";
                }
                else{
                    $rtn = "No hay stock Suficiente";
                }
            }
            else{
                $rtn = "No se encontró la hamburguesa";
            }
        }
        else{
            $rtn = "Alguno de los datos es erroneo";
        }
        return $rtn;
    }

?>