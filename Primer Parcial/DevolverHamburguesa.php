<?php
    include_once "./Venta.php";
    include_once "./Hamburguesa.php";
    include_once "./Cupon.php";
    include_once "./Devolucion.php";

    function DevolverHamburguesa(int $nroPedido,$causaDevolucion,$imagenCliente)
    {
        $rtn = "";
        if(isset($nroPedido) && $nroPedido>0 && isset($causaDevolucion) && $causaDevolucion != "" && isset($imagenCliente) && $imagenCliente["size"]>0)
        {
            $venta = Venta::ObtenerVentaByNumeroPedido($nroPedido);
            if($venta != null)
            {
                $hamburguesa = Hamburguesa::ObtenerHamburguesa($venta->_tipo,$venta->_nombre);
                if($hamburguesa != false)
                {
                    $nuevoCupon = new Cupon($venta->_mail,10/100,new DateTime('now',new DateTimeZone("America/Argentina/Buenos_Aires")),Cupon::ObtenerId("Cupones.json"),true);
                    $nuevoCupon->SaveJSON("Cupones.json");
                    $nuevaDevolucion = new Devolucion(Devolucion::ObtenerId("Devoluciones.json"),$nuevoCupon->_id,$hamburguesa->_id,$nuevoCupon->_fechaCupon,$venta->_mail,$causaDevolucion);
                    $nuevaDevolucion->SaveJSON("Devoluciones.json");
                    $nuevaDevolucion->GuardarImagen("Devoluciones/2023",$nuevaDevolucion->_id,$imagenCliente);
                    $hamburguesa->ModificarStock("+",$venta->_cantidad,"Hamburguesas.json");
                    $rtn = "Devolucion realizada con exito";
                }
                else{
                    $rtn = "No se encuentra la hamburguesa";
                }

            }
            else{
                $rtn = "No se encontró la venta";
            }

        }
        else{
            $rtn = "Alguno de los datos es erroneo";
        }
        return $rtn;

    }

?>