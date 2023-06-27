<?php
include_once "./Venta.php";
include_once "./Hamburguesa.php";

function ModificarVenta(int $nroPedido,$emailUsuario,$nombre,$tipo,$aderezo,int $cantidad)
{
    // Si se coloca el nro de pedido, se modifica
    $rtn = "";
    $rtnModificacion = true;

    if(!empty($nroPedido) && isset($emailUsuario) && filter_var($emailUsuario,FILTER_VALIDATE_EMAIL) && isset($nombre) && isset($tipo) && isset($aderezo) && isset($cantidad))
    {
        $nombre = strtolower($nombre);
        $tipo = strtolower($tipo);
        $aderezo = strtolower($aderezo);
        $pedido = Venta::ObtenerVentaByNumeroPedido($nroPedido);
        if($pedido != null && $pedido->_mail == strtolower($emailUsuario))
        {

            if($nombre != "")
            {
                $pedido->_nombre = $nombre;

            }
            if($tipo != "")
            {
                if(($tipo == Hamburguesa::TIPO_DOBLE || $tipo == Hamburguesa::TIPO_SIMPLE))
                {
                    $pedido->_tipo = $tipo;
                }
                else{
                    $rtnModificacion = false;
                }
                
            }
            if($aderezo != "")
            {
                if(($aderezo == Hamburguesa::ADEREZO_KETCHUP || $aderezo == Hamburguesa::ADEREZO_MAYONESA || $aderezo == Hamburguesa::ADEREZO_MOSTAZA))
                {
                    $pedido->_aderezo = $aderezo;

                }
                else{
                    $rtnModificacion = false;
                }
                
            }
            if($cantidad>0)
            {
                $pedido->_cantidad = $cantidad;
            }
            else{
                $rtnModificacion = false;

            }

            if($rtnModificacion && $pedido->Modificar("Ventas.json"))
            {
                $rtn = "Modificado exitosamente";
            }
            else{
                $rtn = "Hubo un error al modificar";
            }
    
    
        }
        else{
            $rtn = "No se encuentra el pedido con ese nro de pedido o ese mail";
        }
        
    }
    else
    {
        $rtn = "Alguno de los datos no está seteado o es incorrecto";
    }

    return $rtn;



}

?>