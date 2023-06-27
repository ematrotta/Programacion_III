<?php

class Pizza{
    public string $_sabor;
    public float $_precio;
    public string $_tipo;
    public int $_cantidad;
    public int $_id;
    const PIEDRA = "piedra";
    const MOLDE = "molde";


    public function __construct($sabor,$precio,$tipo = self::MOLDE,$_cantidad,$id) {
        $this->_sabor = strtolower($sabor);
        $this->_precio = $precio;
        $this->_tipo = strtolower($tipo);
        $this->_cantidad = $_cantidad;
        $this->_id = $id;
    }

    static function ReadJSON($file)
    {
        $lista = array();
        // Verifico que exista el archivo JSON
        if(file_exists($file)==true)
        {
            $jsonString = file_get_contents($file);
            if($jsonString != false)
            {
                $objetosGuardados = json_decode($jsonString,false);
                foreach($objetosGuardados as $objeto)
                {
                    $objetoGuardado = new Pizza($objeto->_sabor,floatval($objeto->_precio),$objeto->_tipo,intval($objeto->_cantidad),$objeto->_id);
                    array_push($lista,$objetoGuardado);
                }
            }
        }
        
        return $lista;
    }

    function SaveJSON($file)
    {
        // Leo usuarios guardados en el archivo
        $objetosGuardados = Pizza::ReadJSON($file);
        array_push($objetosGuardados,$this);
        $file = fopen($file,"w");
        $jsonString = json_encode($objetosGuardados,JSON_PRETTY_PRINT);
        fwrite($file,$jsonString);
        fclose($file);
    
    }

    static function ObtenerId($file)
    {

        $arrayJson = Pizza::ReadJSON($file);
        $arrayIds = array();
        $rtn = random_int(1,10000);
        if(count($arrayJson)>0)
        {
            foreach($arrayJson as $objeto)
            {
                array_push($arrayIds,intval($objeto->_id));
            }
            $rtn = max($arrayIds)+1;
        }

        return $rtn;
    }

    // static function ValidarSabor($sabor,$file)
    // {
    //     $rtn = false;
    //     $arrayJson = Pizza::ReadJSON($file);
    //     foreach($arrayJson as $objeto)
    //     {
    //         if($objeto->_sabor == strtolower($sabor))
    //         {
    //             $rtn = true;
    //             break;

    //         }
    //     }

    //     return $rtn;
    // }

    // static function ValidarTipo($tipo,$file)
    // {
    //     $rtn = false;
    //     $arrayJson = Pizza::ReadJSON($file);
    //     foreach($arrayJson as $objeto)
    //     {
    //         if($objeto->_tipo == strtolower($tipo))
    //         {
    //             $rtn = true;
    //             break;

    //         }
    //     }

    //     return $rtn;
    // }


    static function ObtenerPizza($tipo,$sabor,$file)
    {
        $rtn = false;
        $arrayJson = Pizza::ReadJSON($file);
        foreach($arrayJson as $objeto)
        {
            if($objeto->_tipo == strtolower($tipo) && $objeto->_sabor == strtolower($sabor))
            {
                $rtn = $objeto;
                break;

            }
        }

        return $rtn;

    }

    function Modificar($file)
    {
        $rtn = false;
        $arrayPizzas = Pizza::ReadJSON($file);
        
        foreach($arrayPizzas as $pizza)
        {
            if($this->_sabor == $pizza->_sabor && $this->_tipo == $pizza->_tipo)
            {
                $pizza->_cantidad += $this->_cantidad;
                $pizza->_precio = $this->_precio;
                $rtn = true;
                break;
            }
            

        }
        if($rtn == true)
        {
            $file = fopen($file,"w");
            $jsonString = json_encode($arrayPizzas,JSON_PRETTY_PRINT);
            fwrite($file,$jsonString);
            fclose($file);

        }

        return $rtn;
    }

    function RestarStock($cantidad,$file)
    {
        $restaStock = false;
        // Leo usuarios guardados en el archivo
        $productosGuardados = Pizza::ReadJSON($file);

        foreach($productosGuardados as $producto)
        {
            if($producto->_id == $this->_id)
            {
                if($producto->_cantidad>=$cantidad)
                {
                    $producto->_cantidad -=$cantidad;
                    $restaStock = true;
                }
                break;
                
            }
        }
        if($restaStock == true)
        {
            $file = fopen($file,"w");
            $jsonString = json_encode($productosGuardados,JSON_PRETTY_PRINT);
            fwrite($file,$jsonString);
            fclose($file);
        }
        return $restaStock;
    }


    static function obtenerPizzasPorSabor($sabor,$file)
    {
        $rtn = array();
        $sabor = strtolower($sabor);
        $arrayJson = Pizza::ReadJSON($file);
        foreach($arrayJson as $objeto)
        {
            if($objeto->_sabor == $sabor)
            {
                array_push($rtn,$objeto);
            }
        }

        return $rtn;

    }

    static function obtenerPizzaById($id,$file)
    {
        $rtn = false;
        $id = intval($id);
        $arrayJson = Pizza::ReadJSON($file);
        foreach($arrayJson as $objeto)
        {
            if($objeto->_id == $id)
            {
                $rtn = $objeto;
                break;
            }
        }

        return $rtn;

    }

    function GuardarImagen($carpeta,$nombre,$archivo){
        $carpeta = "./".$carpeta;
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



















}
?>