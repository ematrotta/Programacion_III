<?php

class Cupon{
    public $_id;
    public $_fechaCupon;
    public $_porcentaje;
    public $_mailCliente;
    public $_estado;
    const PLAZO_VENCIMIENTO = 3;

    public function __construct($mailCliente,$porcentaje,$fechaCupon,$id,$estado) {
        $this->_id = $id;
        $this->_fechaCupon = $fechaCupon;
        $this->_porcentaje = $porcentaje;
        $this->_mailCliente = $mailCliente;
        $this->_estado = $estado;
    }

    static function ObtenerId($file)
    {

        $arrayCupones = Cupon::ReadJSON($file);
        $arrayIds = array();
        $rtn = 1;
        if(count($arrayCupones)>0)
        {
            foreach($arrayCupones as $cupon)
            {
                array_push($arrayIds,$cupon->_id);
            }
            $rtn = max($arrayIds)+1;
        }

        return $rtn;
    }

    static function ReadJSON($file)
    {
        $arrayCupon = array();
        // Verifico que exista el archivo JSON
        if(file_exists($file)==true)
        {
            $jsonString = file_get_contents($file);
            if($jsonString != false)
            {
                $objetosGuardados = json_decode($jsonString,false);
                foreach($objetosGuardados as $objeto)
                {
                    // Constructor
                    $objetoGuardado = new Cupon($objeto->_mailCliente,floatval($objeto->_porcentaje),DateTime::createFromFormat("Y-m-d H:i:s.u",$objeto->_fechaCupon->date),intval($objeto->_id),boolval($objeto->_estado));
                    array_push($arrayCupon,$objetoGuardado);
                }
            }
        }
        
        return $arrayCupon;
    }



    function SaveJSON($file)
    {
        // Leo usuarios guardados en el archivo
        $objetosGuardados = Cupon::ReadJSON($file);
        array_push($objetosGuardados,$this);
        $file = fopen($file,"w");
        $jsonString = json_encode($objetosGuardados,JSON_PRETTY_PRINT);
        fwrite($file,$jsonString);
        fclose($file);
    
    }

    static function obteneraById($id,$file)
    {
        $rtn = false;
        $id = intval($id);
        $arrayCupones = Cupon::ReadJSON($file);
        foreach($arrayCupones as $cupon)
        {
            if($cupon->_id == $id)
            {
                $rtn = $cupon;
                break;
            }
        }
        return $rtn;
    }

    function ValidarVencimiento()
    {
        $rtn = true;
        $diaActual = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $intervalo = date_diff($this->_fechaCupon, $diaActual)->days;
        if($intervalo>Cupon::PLAZO_VENCIMIENTO)
        {
            $rtn = false;
        }
        return $rtn;
    }

    private function ModificarEstado($file)
    {
        $rtn = false;
        $this->_estado = false;
        $cupones = Cupon::ReadJSON($file);
        if(count($cupones)>0)
        {
            foreach($cupones as $cupon)
            {
                if($this->_id == $cupon->_id)
                {
                    $cupon->_estado = $this->_estado;
                    $file = fopen($file,"w");
                    $jsonString = json_encode($cupones,JSON_PRETTY_PRINT);
                    fwrite($file,$jsonString);
                    fclose($file);
                    $rtn = true;

                    break;


                    
                }

            }
        }
        return $rtn;
    }

    function UsarCupon()
    {
        $rtn = false;
        if($this->ValidarVencimiento() && $this->_estado == true)
        {
            $rtn = $this->ModificarEstado("Cupones.json");

        }
        return $rtn;

    }

    function Mostrar()
    {
        return sprintf("Id Cupon: %d<br>Fecha Cupon: %s<br>Porcentaje de descuento: %d<br>Mail del Cliente: %s<br>Estado: %s<br>Vencimiento: %s<br><br>",$this->_id,$this->_fechaCupon->format("d/m/Y H:i"),$this->_porcentaje*100,$this->_mailCliente,$retVal = ($this->_estado) ? "Sin Usar" : "Usado",$retVal = ($this->ValidarVencimiento()) ? "Vigente" : "Vencido");
    }

    static function MostrarCupones()
    {
        $cupones = Cupon::ReadJSON("Cupones.json");
        if(count($cupones)>0)
        {
            foreach($cupones as $cupon)
            {
                echo $cupon->Mostrar();
            }
        }
    }






}
?>