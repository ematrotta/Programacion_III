<?php
class Hamburguesa{

    public $_nombre;
    public $_aderezo;
    public $_tipo;
    public $_id;
    public $_precio;
    public $_cantidad;

    const TIPO_SIMPLE = "simple";
    const TIPO_DOBLE = "doble";
    const ADEREZO_MOSTAZA = "mostaza";
    const ADEREZO_KETCHUP = "ketchup";
    const ADEREZO_MAYONESA = "mayonesa";

    public function __construct($nombre,$aderezo,$tipo,$precio,$cantidad,$id) {
        $this->_nombre = strtolower($nombre);
        $this->_aderezo = strtolower($aderezo);
        $this->_tipo = strtolower($tipo);
        $this->_precio = $precio;
        $this->_cantidad = $cantidad;
        $this->_id = $id;
    }

    static function ReadJSON($file)
    {
        $arrayHamburguesa = array();
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
                    $objetoGuardado = new Hamburguesa($objeto->_nombre,$objeto->_aderezo,$objeto->_tipo,floatval($objeto->_precio),intval($objeto->_cantidad),intval($objeto->_id));
                    array_push($arrayHamburguesa,$objetoGuardado);
                }
            }
        }
        
        return $arrayHamburguesa;
    }

    static function ObtenerHamburguesa($tipo,$nombre)
    {
        $rtn = false;
        $arrayJson = Hamburguesa::ReadJSON("Hamburguesas.json");
        foreach($arrayJson as $objeto)
        {
            if($objeto->_tipo == strtolower($tipo) && $objeto->_nombre == strtolower($nombre))
            {
                $rtn = $objeto;
                break;

            }
        }

        return $rtn;

    }
    function SaveJSON($file)
    {
        // Leo usuarios guardados en el archivo
        $objetosGuardados = Hamburguesa::ReadJSON($file);
        array_push($objetosGuardados,$this);
        $file = fopen($file,"w");
        $jsonString = json_encode($objetosGuardados,JSON_PRETTY_PRINT);
        fwrite($file,$jsonString);
        fclose($file);
    
    }

    function GuardarImagen($carpeta,$nombre,$archivo){
        // Verificar si la carpeta existe
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }
        // Creo la nueva ubicacion para el archivo
        $ubicacionArchivo = $carpeta."/".$archivo["name"];
        // Lo muevo de su carpeta temporal y lo llevo a la carpeta definitiva
        move_uploaded_file($archivo["tmp_name"],$ubicacionArchivo);

        // Obtengo su extensión
        $ext = pathinfo($ubicacionArchivo, PATHINFO_EXTENSION);
        // Guardo la imagen renombrada con el id de usuario para obtenerla facilmente luego
        $nuevoNombreArchivo = $carpeta."/".$nombre.".".$ext;
        rename($ubicacionArchivo,$nuevoNombreArchivo);

    }

    static function ObtenerId($file)
    {

        $arrayHamburguesa = Hamburguesa::ReadJSON($file);
        $arrayIds = array();
        $rtn = 1;
        if(count($arrayHamburguesa)>0)
        {
            foreach($arrayHamburguesa as $hamburguesa)
            {
                array_push($arrayIds,$hamburguesa->_id);
            }
            $rtn = max($arrayIds)+1;
        }

        return $rtn;
    }

    function Modificar($file,$cantidad,$precio)
    {
        $rtn = false;
        $arrayHamburguesa = Hamburguesa::ReadJSON($file);
        
        foreach($arrayHamburguesa as $hamburguesa)
        {
            if($this->_id == $hamburguesa->_id)
            {
                $hamburguesa->_cantidad += $cantidad;
                $hamburguesa->_precio = $precio;
                $rtn = true;
                break;
            }
            
        }
        if($rtn == true)
        {
            $file = fopen($file,"w");
            $jsonString = json_encode($arrayHamburguesa,JSON_PRETTY_PRINT);
            fwrite($file,$jsonString);
            fclose($file);

        }

        return $rtn;
    }


    // Metodos Utilies*******************************************************************

    function ModificarStock($simbolo = "-",$cantidad,$file)
    {
        
        $modificarStock = false;
        if($simbolo == "+" || $simbolo == "-")
        {
            // Leo hamburguesas guardados en el archivo
            $arrayHamburguesa = Hamburguesa::ReadJSON($file);

            foreach($arrayHamburguesa as $hamburguesa)
            {
                if($hamburguesa->_id == $this->_id)
                {
                    switch($simbolo)
                    {
                        case "-":
                            if($hamburguesa->_cantidad>=$cantidad)
                            {
                                $hamburguesa->_cantidad -=$cantidad;
                                $modificarStock = true;
                            }
                            break;
                        case "+":
                            $hamburguesa->_cantidad +=$cantidad;
                            $modificarStock = true;
                            break;
                    }
                    break;
                    
                }
            }
            if($modificarStock == true)
            {
                $file = fopen($file,"w");
                $jsonString = json_encode($arrayHamburguesa,JSON_PRETTY_PRINT);
                fwrite($file,$jsonString);
                fclose($file);
            }

        }


        return $modificarStock;
    }

    static function obtenerHamburguesaById($id,$file)
    {
        $rtn = false;
        $id = intval($id);
        $arrayHamburguesa = Hamburguesa::ReadJSON($file);
        foreach($arrayHamburguesa as $hamburguesa)
        {
            if($hamburguesa->_id == $id)
            {
                $rtn = $hamburguesa;
                break;
            }
        }
        return $rtn;
    }


    function MoverImagen($carpetaOrigen,$carpetaDestino,$fileName){
        $rtn = false;

        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }
        $files = scandir($carpetaOrigen);
        // $nombreArchivo = $pizza->_tipo."_".$pizza->_sabor."_".substr($usuario->_mail,0,stripos($usuario->_mail, '@'))."_".$this->_fechaCompra->format("d_m_Y H_i");
        foreach ($files as $file) {

            $resultado = strpos($file,$fileName);
            if ($resultado !== false) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                rename($carpetaOrigen."/".$file,$carpetaDestino."/".$fileName.".".$ext);
                $rtn = true;                
                break;
            }

        }

        return $rtn;


    }

    function Mostrar(){

        return sprintf("<br>String Uno: %s<br>String Dos: %s<br>Id: %d<br>Precio: %.2f<br>Cantidad: %d",$this->_aderezo,$this->_tipo,$this->_id,$this->_precio,$this->_cantidad);

    }



}


?>