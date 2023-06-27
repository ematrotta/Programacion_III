<?php

class Venta{
    public int $_nroPedido;
    public int $_idVenta;
    public DateTime $_fechaCompra;
    public int $_idUsuario;
    public int $_idPizza;
    public int $_cantidadPizza;

    public function __construct($fechaCompra,$idVenta,$nroPedido,$idUsuario,$idPizza,$cantidadPizza) {
        $this->_fechaCompra = $fechaCompra;
        $this->_idVenta = $idVenta;
        $this->_nroPedido = $nroPedido;
        $this->_idUsuario = $idUsuario;
        $this->_idPizza = $idPizza;
        $this->_cantidadPizza = $cantidadPizza;
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
                    $objetoGuardado = new Venta(DateTime::createFromFormat("Y-m-d H:i:s.u",$objeto->_fechaCompra->date),intval($objeto->_idVenta),intval($objeto->_nroPedido),intval($objeto->_idUsuario),intval($objeto->_idPizza),intval($objeto->_cantidadPizza));
                    array_push($lista,$objetoGuardado);
                }
            }
        }
        
        return $lista;
    }

    function SaveJSON($file)
    {
        // Leo usuarios guardados en el archivo
        $objetosGuardados = Venta::ReadJSON($file);
        array_push($objetosGuardados,$this);
        $file = fopen($file,"w");
        $jsonString = json_encode($objetosGuardados,JSON_PRETTY_PRINT);
        fwrite($file,$jsonString);
        fclose($file);
    
    }


    static function ObtenerId($file)
    {

        $arrayJson = Venta::ReadJSON($file);
        $arrayIds = array();
        $rtn = random_int(1,10000);
        if(count($arrayJson)>0)
        {
            foreach($arrayJson as $objeto)
            {
                array_push($arrayIds,intval($objeto->_idVenta));
            }
            $rtn = max($arrayIds)+1;
        }

        return $rtn;
    }

    static function ObtenerNroPedido($file)
    {

        $arrayJson = Venta::ReadJSON($file);
        $arrayPedido = array();
        $rtn = 1;
        if(count($arrayJson)>0)
        {
            foreach($arrayJson as $objeto)
            {
                array_push($arrayPedido,intval($objeto->_nroPedido));
            }
            $rtn = max($arrayPedido)+1;
        }

        return $rtn;
    }

    static function ObtenerCantidadPizzasVendidas()
    {
        $rtn = 0;
        $arrayVentas = Venta::ReadJSON("Ventas.json");
        foreach($arrayVentas as $venta)
        {
            $rtn += $venta->_cantidadPizza;
        }

        return $rtn;

    }

    static function ObtenerVentasByMailUsuario(string $mail)
    {
        $rtn = false;
        $usuario = Usuario::ObtenerUsuarioByMail($mail,"Usuarios.json");
        $arrayVentas = Venta::ReadJSON("Ventas.json");
        $arrayVentasUsuario = array();
        if($usuario != false)
        {
            foreach($arrayVentas as $venta){
                if($venta->_idUsuario == $usuario->_id){
                    array_push($arrayVentasUsuario,$venta);
                }
            }
            $rtn = $arrayVentasUsuario;

        }
        return $rtn;
    }

    static function ObtenerVentaPorPedido(int $nroPedido)
    {
        $rtn = false;
        $arrayVentas = Venta::ReadJSON("Ventas.json");
        foreach($arrayVentas as $venta){
            if($venta->_nroPedido == $nroPedido){
                $rtn = $venta;
                break;
            }
        }
        return $rtn;
    }

    static function ObtenerVentasBySabor(string $sabor)
    {
        $sabor = strtolower($sabor);
        $pizza = null;
        $arrayVentas = Venta::ReadJSON("Ventas.json");
        $arrayPizzasPorSabor = array();
        foreach($arrayVentas as $venta){

            $pizza = Pizza::obtenerPizzaById($venta->_idPizza,"Pizza.json");
            if($pizza != false){
                if($pizza->_sabor == $sabor){
                    array_push($arrayPizzasPorSabor,$venta);
                }
            }


        }
        return $arrayPizzasPorSabor;
    }

    static function ObtenerVentasEntreFechas($fechaInicio,$fechaFin)
    {
        $arrayVentas = Venta::ReadJSON("Ventas.json");
        $arrayPizzas = array();
        if(get_class($fechaInicio) == "DateTime" && get_class($fechaFin) == "DateTime")
        {
            foreach($arrayVentas as $venta){

                if($venta->_fechaCompra->setTime(0,0,0)>=$fechaInicio && $venta->_fechaCompra->setTime(0,0,0)<=$fechaFin){
                    array_push($arrayPizzas,$venta);
                }
    
            }

        }

        return $arrayPizzas;
    }

    public function Mostrar(){

        $usuario = Usuario::ObtenerUsuarioById($this->_idUsuario,"Usuarios.json");
        $pizza = Pizza::obtenerPizzaById($this->_idPizza,"Pizza.json");

        if($pizza != false && $usuario != false)
        {
            return sprintf("<br>Nro de venta: %d<br>Id de Venta: %d<br>Fecha de compra: %s<br>Tipo Pizza: %s<br>Sabor Pizza: %s<br>Usuario: %s<br>Cantidad: %d",$this->_nroPedido,$this->_idVenta,$this->_fechaCompra->format("d-m-Y"),$pizza->_tipo,$pizza->_sabor,$usuario->_mail,$this->_cantidadPizza);

        }


    }

    public static function OrdenarVentasPorSabor(&$arrayVentas)
    {
        $rtn = usort($arrayVentas, function($a, $b) {
            
            $pizzaA = Pizza::obtenerPizzaById($a->_idPizza,"Pizza.json");
            $pizzaB = Pizza::obtenerPizzaById($b->_idPizza,"Pizza.json");

            if($pizzaA != false && $pizzaB != false)
            {
                $resultado = strcmp($pizzaA->_sabor,$pizzaB->_sabor);
                echo "El sabor de Pizza A es: ".$pizzaA->_sabor." y el de Pizza B es: ".$pizzaB->_sabor;
                echo "El resultado es: ".$resultado."<br>";
                return $resultado;
            }
            
          });
        
          return $rtn;
        

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

    function MoverImagen($carpetaOrigen,$carpetaDestino){
        $rtn = false;

        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }
        $files = scandir($carpetaOrigen);
        $pizza = Pizza::obtenerPizzaById($this->_idPizza,"Pizza.json");
        $usuario = Usuario::ObtenerUsuarioById($this->_idUsuario,"Usuarios.json");
        
        if($pizza != false && $usuario != false)
        {
            $nombreArchivo = $pizza->_tipo."_".$pizza->_sabor."_".substr($usuario->_mail,0,stripos($usuario->_mail, '@'))."_".$this->_fechaCompra->format("d_m_Y H_i");
            foreach ($files as $file) {

                $resultado = strpos($file,$nombreArchivo);
                if ($resultado !== false) {
                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                    rename($carpetaOrigen."/".$file,$carpetaDestino."/".$nombreArchivo.".".$ext);
                    $rtn = true;                
                    break;
                }

            }

        }

        return $rtn;


    }




    
}
?>