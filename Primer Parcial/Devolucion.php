<?php
include_once "./Cupon.php";
class Devolucion{
    public $_id;
    public $_idHamburguesa;
    public $_idCupon;
    public $_fechaDevolucion;
    public $_mailUsuario;
    public $_motivoDevolucion;

    public function __construct($id,$idCupon,$idHamburguesa,$fechaDevolucion,$mailUsuario,$motivoDevolucion) {
        $this->_id = $id;
        $this->_idCupon = $idCupon;
        $this->_idHamburguesa = $idHamburguesa;
        $this->_fechaDevolucion = $fechaDevolucion;
        $this->_mailUsuario = $mailUsuario;
        $this->_motivoDevolucion = $motivoDevolucion;

    }

    static function ObtenerId($file)
    {

        $arrayDevoluciones = Devolucion::ReadJSON($file);
        $arrayIds = array();
        $rtn = 1;
        if(count($arrayDevoluciones)>0)
        {
            foreach($arrayDevoluciones as $devolucion)
            {
                array_push($arrayIds,$devolucion->_id);
            }
            $rtn = max($arrayIds)+1;
        }

        return $rtn;
    }

    static function ReadJSON($file)
    {
        $arrayDevolucion = array();
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
                    $objetoGuardado = new Devolucion(intval($objeto->_id),intval($objeto->_idCupon),intval($objeto->_idHamburguesa),DateTime::createFromFormat("Y-m-d H:i:s.u",$objeto->_fechaDevolucion->date),$objeto->_mailUsuario,$objeto->_motivoDevolucion);
                    array_push($arrayDevolucion,$objetoGuardado);
                }
            }
        }
        
        return $arrayDevolucion;
    }



    function SaveJSON($file)
    {
        // Leo usuarios guardados en el archivo
        $objetosGuardados = Devolucion::ReadJSON($file);
        array_push($objetosGuardados,$this);
        $file = fopen($file,"w");
        $jsonString = json_encode($objetosGuardados,JSON_PRETTY_PRINT);
        fwrite($file,$jsonString);
        fclose($file);
    
    }

    function GuardarImagen($carpeta,$nombre,$archivo){
        $carpeta = "./".$carpeta;
        // Verificar si la carpeta existe
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }
        // Creo la nueva ubicacion para el archivo
        $ubicacionArchivo = "./".$carpeta."/".$archivo["name"];
        // Lo muevo de su carpeta temporal y lo llevo a la carpeta definitiva
        move_uploaded_file($archivo["tmp_name"],$ubicacionArchivo);

        // Obtengo su extensión
        $ext = pathinfo($ubicacionArchivo, PATHINFO_EXTENSION);
        // Guardo la imagen renombrada con el id de usuario para obtenerla facilmente luego
        $nuevoNombreArchivo = "./".$carpeta."/".$nombre.".".$ext;
        rename($ubicacionArchivo,$nuevoNombreArchivo);

    }

    function Mostrar()
    {
        $cupon = Cupon::obteneraById($this->_idCupon,"Cupones.json");
        return sprintf("Id Devolucion: %d<br>Fecha Devolucion: %s<br>Mail Usuario: %s<br>Motivo Devolucion: %s<br>Cupon: <br><br>%s<br>",$this->_id,$this->_fechaDevolucion->format("d/m/Y H:i"),$this->_mailUsuario,$this->_motivoDevolucion,($cupon) ? $cupon->Mostrar() : "No hay cupones para mostrar");

    }

    static function MostrarDevoluciones()
    {
        $arrayDevoluciones = Devolucion::ReadJSON("Devoluciones.json");
        foreach($arrayDevoluciones as $devolucion)
        {
            echo $devolucion->Mostrar();
        }
    }
}
?>