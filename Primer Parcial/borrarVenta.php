<?php
include_once "./Venta.php";
include_once "./Hamburguesa.php";

function BorrarVenta(int $nroPedido)
{
    $rtn = "";
    $venta = Venta::ObtenerVentaByNumeroPedido($nroPedido);
    if($nroPedido > 0)
    {
        if($venta != null)
        {
            $hamburguesa = Hamburguesa::ObtenerHamburguesa($venta->_tipo,$venta->_nombre);
            if($hamburguesa)
            {
                $resultadoResta = $hamburguesa->ModificarStock("+",$venta->_cantidad,"Hamburguesas.json");
                if($resultadoResta)
                {
                    $venta->MoverImagen("./ImagenesDeLaVenta/2023","./BACKUPVENTAS/2023",$venta->_tipo."_".$venta->_nombre."_".substr($venta->_mail,0,stripos($venta->_mail, '@'))."_".$venta->_fecha->format("d_m_Y H_i"));
                    $venta->Eliminar("Ventas.json");
                    $rtn = "Eliminación exitosa";
                }
                else{
                    $rtn = "No se pudo eliminar";
                }
    
            }
            else{
                $rtn = "No se encontro la hamburguesa";
            }
    
        }
        else{
            $rtn = "No se encontró la venta";
        }

    }
    else{
        $rtn = "No se seleccionó un número de pedido correcto. Debe ser mayor a 0";
    }

    return $rtn;

}


?>