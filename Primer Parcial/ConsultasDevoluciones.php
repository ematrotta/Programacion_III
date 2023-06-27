<?php


include_once "./Cupon.php";
include_once "./Devolucion.php";
function ConsultasDevoluciones($tipoListado)
{
    $rtn = "";
    if(isset($tipoListado) && !empty($tipoListado))
    {
        switch($tipoListado)
        {
            case "a":
                echo "Listar devoluciones con cupones<br><br>";
                foreach(Devolucion::ReadJSON("Devoluciones.json") as $devolucion)
                {
                    $cupon = Cupon::obteneraById($devolucion->_idCupon,"Cupones.json");
                    echo sprintf("Id Devolucion: %d<br>Fecha Devolucion: %s<br>Mail Usuario: %s<br>Motivo Devolucion: %s<br>",$devolucion->_id,$devolucion->_fechaDevolucion->format("d/m/Y H:i"),$devolucion->_mailUsuario,$devolucion->_motivoDevolucion);
                    if($cupon)
                    {
                        echo "Id Cupon: ".$cupon->_id."<br><br>";
                    }
                }
                break;
            case "b";
                echo "Listar solo los cupones y su estado<br><br>";
                Cupon::MostrarCupones();
                break;
            case "c":
                echo "Listar devoluciones con cupones y estado <br><br>";
                Devolucion::MostrarDevoluciones();
                break;
            default:
                $rtn ="Error al colocar el tipo de listado, debe ser: a, b o c";
                break;
        }
    }
    else{
        $rtn = "Se colocó mal el tipo de listado";
    }
    return $rtn;
}

?>