<?php



    switch($_SERVER["REQUEST_METHOD"])
    {
        case "POST":
            switch(strtolower($_POST["archivoPhp"]))
            {
                case "hamburguesacarga":
                    include_once "./HamburguesaCarga.php";
                    echo(HamburguesaCarga($_POST["nombre"],$_POST["aderezo"],$_POST["tipo"],intval($_POST["cantidad"]),floatval($_POST["precio"]),$_FILES["file"]));
                    break;
                case "hamburguesaconsultar":
                    include_once "./HamburguesasConsultar.php";
                    echo(HamburguesaConsultar(strtolower($_POST["nombre"]),strtolower($_POST["tipo"])));
                    break;
                case "altaventa":
                    include_once "./AltaVenta.php";
                    echo(AltaVenta(strtolower($_POST["nombre"]),strtolower($_POST["mailUsuario"]),strtolower($_POST["tipo"]),strtolower($_POST["aderezo"]),intval($_POST["cantidad"]),$_FILES["file"],intval($_POST["nroCupon"])));
                    break;
                case "devolverhamburguesa":
                    include_once "./DevolverHamburguesa.php";
                    echo (DevolverHamburguesa(intval($_POST["nroPedido"]),$_POST["causaDevolucion"],$_FILES["imagen"]));
                    break;
                default:
                    echo "No se seleccionó el tipo de archivo php correcto";
                    break;
            }
            
            break;
        case "GET":
            switch(strtolower($_GET["archivoPhp"]))
            {
                case "consultasventas":
                    include_once "./ConsultasVentas.php";
                    echo(ConsultasVentas($_GET["tipo_listado"],$_GET["fechaInicial"],$_GET["fechaFinal"],$_GET["datoAdicional"]));
                    break;
                case "consultasdevoluciones":
                    include_once "./ConsultasDevoluciones.php";
                    echo(ConsultasDevoluciones($_GET["tipo_listado"]));
                    break;
                default:
                    echo "No se seleccionó el tipo de archivo php correcto";
                    break;
            }
            break;
        case "PUT":
            // $_PUT = json_decode(file_get_contents("php://input"), true);
            switch(strtolower($_GET["archivoPhp"]))
            {

                case "modificarventa":
                    include_once "./ModificarVenta.php";
                    echo(ModificarVenta(intval($_GET["nroPedido"]),$_GET["mail"],$_GET["nombre"],$_GET["tipo"],$_GET["aderezo"],intval($_GET["cantidad"])));
                    break;
                default:
                    echo "No se seleccionó el tipo de archivo php correcto";
                    break;
            }


            break;
        case "DELETE":
            switch(strtolower($_GET["archivoPhp"]))
            {
                case "borrarventa":
                    include_once "./borrarVenta.php";
                    echo(BorrarVenta(intval($_GET["nroPedido"])));
                    break;
                default:
                    echo "No se seleccionó el tipo de archivo php correcto";
                    break;
            }

            
            break;

    }
?>