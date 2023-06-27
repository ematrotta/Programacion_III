<?php
include_once "./Hamburguesa.php";

class Venta{

    public $_nombre;
    public $_mail;
    public $_tipo;
    public $_aderezo;
    public $_fecha;
    public $_cantidad;
    public $_nroPedido;
    public $_id;
    public $_importeFinal;
    public $_descuento;


    public function __construct($nombre,$mail,$tipo,$aderezo,$fecha,$cantidad,$nroPedido,$id,$importeFinal = null,$descuento = null) {
        $this->_nombre = $nombre;
        $this->_mail = $mail;
        $this->_tipo = $tipo;
        $this->_aderezo = $aderezo;
        $this->_fecha = $fecha;
        $this->_cantidad = $cantidad;
        $this->_nroPedido = $nroPedido;
        $this->_id = $id;
        $this->_descuento = 0;
        $this->_importeFinal = $this->AsignarImporte($tipo,$nombre);
        $this->__construct2($importeFinal,$descuento);
    }

    private function __construct2($importeFinal,$descuento)
    {
        if(isset($importeFinal) && isset($descuento) && !empty($importeFinal))
        {
            $this->_importeFinal = $importeFinal;
            $this->_descuento = $descuento;
        }
    }

    static function ReadJSON($file)
    {
        $arrayVenta = array();
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
                    $objetoGuardado = new Venta($objeto->_nombre,$objeto->_mail,$objeto->_tipo,$objeto->_aderezo,DateTime::createFromFormat("Y-m-d H:i:s.u",$objeto->_fecha->date),intval($objeto->_cantidad),intval($objeto->_nroPedido),intval($objeto->_id),floatval($objeto->_importeFinal),floatval($objeto->_descuento));
                    array_push($arrayVenta,$objetoGuardado);
                }
            }
        }
        
        return $arrayVenta;
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

        $arrayVenta = Venta::ReadJSON($file);
        $arrayIds = array();
        $rtn = 1;
        if(count($arrayVenta)>0)
        {
            foreach($arrayVenta as $venta)
            {
                array_push($arrayIds,$venta->_id);
            }
            $rtn = max($arrayIds)+1;
        }

        return $rtn;
    }

    static function ObtenerNroPedido($file)
    {

        $arrayVenta = Venta::ReadJSON($file);
        $arrayIds = array();
        $rtn = 1;
        if(count($arrayVenta)>0)
        {
            foreach($arrayVenta as $venta)
            {
                array_push($arrayIds,$venta->_nroPedido);
            }
            $rtn = max($arrayIds)+1;
        }

        return $rtn;
    }

    function SumarStock($file)
    {
        $rtn = false;
        $arrayVenta = Venta::ReadJSON($file);
        
        foreach($arrayVenta as $venta)
        {
            if($this->_id == $venta->_id)
            {
                $venta->_cantidad += $this->_cantidad;
                $rtn = true;
                break;
            }
            
        }
        if($rtn == true)
        {
            $file = fopen($file,"w");
            $jsonString = json_encode($arrayVenta,JSON_PRETTY_PRINT);
            fwrite($file,$jsonString);
            fclose($file);

        }

        return $rtn;
    }

    function RestarStock($cantidad,$file)
    {
        $restaStock = false;
        // Leo usuarios guardados en el archivo
        $arrayVenta = Venta::ReadJSON($file);

        foreach($arrayVenta as $venta)
        {
            if($venta->_id == $this->_id)
            {
                if($venta->_cantidad>=$cantidad)
                {
                    $venta->_cantidad -=$cantidad;
                    $restaStock = true;
                }
                break;
                
            }
        }
        if($restaStock == true)
        {
            $file = fopen($file,"w");
            $jsonString = json_encode($arrayVenta,JSON_PRETTY_PRINT);
            fwrite($file,$jsonString);
            fclose($file);
        }
        return $restaStock;
    }

    static function obtenerVentaById($id,$file)
    {
        $rtn = false;
        $id = intval($id);
        $arrayVenta = Venta::ReadJSON($file);
        foreach($arrayVenta as $venta)
        {
            if($venta->_id == $id)
            {
                $rtn = $venta;
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
        $ubicacionArchivo = "./".$carpeta."/".$archivo["name"];
        // Lo muevo de su carpeta temporal y lo llevo a la carpeta definitiva
        move_uploaded_file($archivo["tmp_name"],$ubicacionArchivo);

        // Obtengo su extensión
        $ext = pathinfo($ubicacionArchivo, PATHINFO_EXTENSION);
        // Guardo la imagen renombrada con el id de usuario para obtenerla facilmente luego
        $nuevoNombreArchivo = "./".$carpeta."/".$nombre.".".$ext;
        rename($ubicacionArchivo,$nuevoNombreArchivo);

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

        return sprintf("<br>Nombre usuario: %s<br>Mail Usuario: %s<br>Tipo: %s<br>Aderezo: %s<br>Fecha: %s<br>Cantidad: %d<br>Nro de pedido: %d<br>Id: %d\n",$this->_nombre,$this->_mail,$this->_tipo,$this->_aderezo,$this->_fecha->format("d/m/Y"),$this->_cantidad,$this->_nroPedido,$this->_id);

    }

    static function ObtenerVentasEntreFechas(DateTime $fechaInicio,DateTime $fechaFin,$file)
    {
        // No incluye las ventas de fin de fecha
        $rtn = array();

        if($fechaInicio<=$fechaFin)
        {
            $arrayVentas = Venta::ReadJSON($file);
            foreach($arrayVentas as $venta)
            {
                if($venta->_fecha->setTime(0,0,0)>= $fechaInicio->setTime(0,0,0) && $venta->_fecha->setTime(0,0,0)<$fechaFin->setTime(0,0,0))
                {
                    array_push($rtn,$venta);
                }
            }

        }

        return $rtn;
    }
    static function ObtenerVentasPorUsuario($mailUsuario,$file)
    {
        $rtn = array();

        if(isset($mailUsuario)&& !empty($mailUsuario))
        {
            $mailUsuario = strtolower($mailUsuario);
            $arrayVentas = Venta::ReadJSON($file);
            foreach($arrayVentas as $venta)
            {
                if($venta->_mail == $mailUsuario)
                {
                    array_push($rtn,$venta);
                }
            }

        }

        return $rtn;

    }

    static function ObtenerVentasPorAderezo($aderezo,$file)
    {
        $rtn = array();

        if(isset($aderezo)&& !empty($aderezo))
        {
            $aderezo = strtolower($aderezo);
            $arrayVentas = Venta::ReadJSON($file);
            foreach($arrayVentas as $venta)
            {
                if($venta->_aderezo == $aderezo)
                {
                    array_push($rtn,$venta);
                }
            }

        }

        return $rtn;

    }
    static function ObtenerVentasPorTipo($tipo,$file)
    {
        $rtn = array();

        if(isset($tipo)&& !empty($tipo))
        {
            $tipo = strtolower($tipo);
            $arrayVentas = Venta::ReadJSON($file);
            foreach($arrayVentas as $venta)
            {
                if($venta->_tipo == $tipo)
                {
                    array_push($rtn,$venta);
                }
            }

        }
        return $rtn;
    }
    static function ObtenerVentaByNumeroPedido(int $nroDePedido)
    {
        $rtn = null;
        if(isset($nroDePedido) && !empty($nroDePedido))
        {
            $ventas=Venta::ReadJSON("Ventas.json");
            foreach($ventas as $venta)
            {
                if($venta->_nroPedido == $nroDePedido)
                {
                    $rtn = $venta;
                    break;
                }
            }

        }
        return $rtn;

    }

    static function SortByName(&$array)
    {
        $rtn = false;

        if(isset($array) )
        {
            $rtn = usort($array,function($a,$b){
                return strcmp($a->_nombre,$b->_nombre);
            });

        }
        return $rtn;
    }

    function Eliminar($file)
    {
        $rtn = false;
        $arrayVentas = Venta::ReadJSON($file);
        foreach($arrayVentas as $index=>$venta)
        {
            if($venta->_id == $this->_id)
            {
                array_splice($arrayVentas,$index,1);
                $file = fopen($file,"w");
                $jsonString = json_encode($arrayVentas,JSON_PRETTY_PRINT);
                fwrite($file,$jsonString);
                fclose($file);
                $rtn = true;
                break;

            }
        }
        return $rtn;
    }

    function Modificar($file)
    {
        $rtn = false;
        $arrayVentas = Venta::ReadJSON($file);
        foreach($arrayVentas as $key=>$venta)
        {
            if($venta->_id == $this->_id)
            {
                $arrayVentas[$key]->_nombre = $this->_nombre;
                $arrayVentas[$key]->_tipo = $this->_tipo;
                $arrayVentas[$key]->_aderezo = $this->_aderezo;
                $arrayVentas[$key]->_cantidad = $this->_cantidad;

                $file = fopen($file,"w");
                $jsonString = json_encode($arrayVentas,JSON_PRETTY_PRINT);
                fwrite($file,$jsonString);
                fclose($file);
                $rtn = true;

                break;

            }
        }
        return $rtn;

    }

    function AsignarImporte($tipoHamburguesa,$nombreHamburguesa)
    {
        $rtn = 0;
        $hamburguesa = Hamburguesa::ObtenerHamburguesa($tipoHamburguesa,$nombreHamburguesa);
        if($hamburguesa != false){
            $rtn = $this->_cantidad*$hamburguesa->_precio;

        }
        return $rtn;
    }

    function AplicarDescuento($descuento)
    {
        $rtn = false;
        if(isset($descuento) && !empty($descuento) && is_float($descuento))
        {
            $this->_descuento = $descuento;
            $this->_importeFinal -= $descuento;
            $rtn = true;
        }
        return $rtn;
    }





}


?>