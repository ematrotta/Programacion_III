<?php
    include_once "./Hamburguesa.php";

    function HamburguesaCarga($nombre,$aderezo,$tipo,int $cantidad,float $precio,$file = null)
    {
        $rtn = "";
        $nombre = strtolower($nombre);
        $aderezo = strtolower($aderezo);
        $tipo = strtolower($tipo);

        if(isset($aderezo) && isset($tipo) && isset($cantidad) && isset($precio) && ($aderezo == Hamburguesa::ADEREZO_KETCHUP || $aderezo == Hamburguesa::ADEREZO_MOSTAZA ||
         $aderezo == Hamburguesa::ADEREZO_MAYONESA) && ($tipo == Hamburguesa::TIPO_DOBLE || $tipo == Hamburguesa::TIPO_SIMPLE))
        {
            $hamburguesa = Hamburguesa::ObtenerHamburguesa($tipo,$nombre);
            if($hamburguesa == false)
            {
                $hamburguesa = new Hamburguesa($nombre,$aderezo,$tipo,$precio,$cantidad,Hamburguesa::ObtenerId("Hamburguesas.json"));
                $hamburguesa->SaveJson("Hamburguesas.json");
                if($file != null)
                {

                    $hamburguesa->GuardarImagen("./ImagenesDeHamburguesas/2023",$hamburguesa->_tipo."_".$hamburguesa->_nombre,$file);
                    
                }
                $rtn = "Se dio de alta Hamburguesa";

            }
            else{
                $hamburguesa->Modificar("Hamburguesas.json",$cantidad,$precio);
                $rtn = "Se modificaron las cantidades y actualizó el precio";
            }


        }
        else{
            $rtn = "Alguno de los parámetros no es correcto";
        }
        return $rtn;

    }

?>