<?php
include_once "./db/AccesoDatos.php";


class Mesa{

    public $_idMesa;
    public $_mozo;
    public $_importeTotal;
    public $_nombreCliente;
    public $_estado;
    public $_fechaApertura;
    public $_fechaCierre;

    
    const ESTADO_ESPERANDO = "cliente esperando pedido";
    const ESTADO_COMIENDO = "cliente comiendo";
    const ESTADO_PAGO = "cliente pagando";
    const ESTADO_CANCELADO = "cancelado";
    const ESTADO_CERRADO = "cerrada";

    private static $objetoPDO;


    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (ID_MESA,ID_MOZO,TOTAL,NOMBRE_CLIENTE,ESTADO,FECHA_APERTURA) VALUES (?,?,?,?,?,?)");
        // $consulta = self::consultasDB("INSERT INTO mesas (ID_MESA,ID_MOZO,TOTAL,NOMBRE_CLIENTE,ESTADO,FECHA_APERTURA) VALUES (?,?,?,?,?,?)");
        $fechaApertura = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $importeInicial = 0;
        $estado = self::ESTADO_ESPERANDO;
        $strFechaApertura = date_format($fechaApertura, 'Y-m-d H:i:s');
        $idMesaUnico = $this->crearIdUnico();

        $consulta->bindParam(1, $idMesaUnico);
        $consulta->bindParam(2, $this->_mozo->_idUsuario);
        $consulta->bindParam(3, $importeInicial);
        $consulta->bindParam(4, $this->_nombreCliente);
        $consulta->bindParam(5, $estado);
        $consulta->bindParam(6, $strFechaApertura);

        $consulta->execute();
        $consulta->closeCursor();

        // Retornar el código único de mesa
        // return $objAccesoDatos->obtenerUltimoId();
        return $idMesaUnico;
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas");
        // $consulta = self::consultasDB("SELECT * FROM mesas");
        $consulta->execute();

        
        // $consulta->fetchAll(PDO::FETCH_OBJ);
        // return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
        $arrayMesas = array();
        $objetosObtenidos = $consulta->fetchAll(PDO::FETCH_OBJ);
        $consulta->closeCursor();

        foreach($objetosObtenidos as $prototipo)
        {
            array_push($arrayMesas,Mesa::transformarPrototipo($prototipo));
        }
        // $consulta->closeCursor();

        return $arrayMesas;
    }

    public static function obtenerMesa($id)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE ID_MESA = ?");
        // $consulta = self::consultasDB("SELECT * FROM mesas WHERE ID_MESA = ?");
        $id = strval($id);
        $consulta->bindParam(1, $id);
        $consulta->execute();
        
        // return $consulta->fetchObject('Mesa');
        // return $consulta->fetch(PDO::FETCH_OBJ);
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        $consulta->closeCursor();
        if($prototipeObject != false)
        {
            $rtn = Mesa::transformarPrototipo($prototipeObject);
        }
        // $consulta->closeCursor();
        
        return $rtn;
    }

    private static function transformarPrototipo($prototipo)
    {   
        $mesa = new Mesa();
        $mesa->_idMesa = $prototipo->ID_MESA;
        $mesa->_mozo = Usuario::obtenerUsuario($prototipo->ID_MOZO);
        $mesa->_fechaApertura = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_APERTURA,new DateTimeZone("America/Argentina/Buenos_Aires"));
        if($prototipo->FECHA_CIERRE != NULL)
        {
            $mesa->_fechaCierre = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_CIERRE,new DateTimeZone("America/Argentina/Buenos_Aires"));

        }
        else{
            $mesa->_fechaCierre = null;
        }
        $mesa->_importeTotal = $prototipo->TOTAL;
        $mesa->_nombreCliente = $prototipo->NOMBRE_CLIENTE;
        $mesa->_estado = $prototipo->ESTADO;
        return $mesa;

    }

    public static function modificarMesa($mesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET ID_MOZO = ?, NOMBRE_CLIENTE = ?  WHERE ID_MESA = ? AND FECHA_CIERRE IS NULL");
        // $consulta = self::consultasDB("UPDATE mesas SET ID_MOZO = ?, NOMBRE_CLIENTE = ?  WHERE ID_MESA = ? AND FECHA_CIERRE = NULL");
        $consulta->bindParam(1, $mesa->_mozo->_idUsuario);
        $consulta->bindParam(2, $mesa->_nombreCliente);
        $consulta->bindParam(3, $mesa->_idMesa);
        $consulta->execute();
        $consulta->closeCursor();
    }

    private static function borrarMesa($mesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET FECHA_CIERRE = ?, ESTADO = ? WHERE ID_MESA = ? AND FECHA_CIERRE IS NULL");
        // $consulta = self::consultasDB("UPDATE mesas SET FECHA_CIERRE = ?, ESTADO = ? WHERE ID_MESA = ? AND FECHA_CIERRE = NULL");
        $fecha = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $fechaString = date_format($fecha, 'Y-m-d H:i:s');
        $estado = self::ESTADO_CANCELADO;
        $idMesa = $mesa->_idMesa;
        $consulta->bindParam(1, $fechaString);
        $consulta->bindParam(2, $estado);
        $consulta->bindParam(3, $idMesa);
        $consulta->execute();
        $consulta->closeCursor();
    }

    private static function crearIdUnico()
    {

        return substr(md5(uniqid(mt_rand(), true)), 0, 5);

    }

    public static function modificarEstado($mesa,$estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        switch($estado)
        {
            case self::ESTADO_CANCELADO:
                $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET FECHA_CIERRE = ?, ESTADO = ? WHERE ID_MESA = ? AND FECHA_CIERRE IS NULL");
                // $consulta = self::consultasDB("UPDATE mesas SET FECHA_CIERRE = ?, ESTADO = ? WHERE ID_MESA = ? AND FECHA_CIERRE = NULL");
                $fecha = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
                $fechaString = date_format($fecha, 'Y-m-d H:i:s');
                $estado = self::ESTADO_CANCELADO;
                $idMesa = $mesa->_idMesa;
                $consulta->bindParam(1, $fechaString);
                $consulta->bindParam(2, $estado);
                $consulta->bindParam(3, $idMesa);
                $consulta->execute();
                $consulta->closeCursor();
                break;
            case self::ESTADO_CERRADO:
                $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET FECHA_CIERRE = ?, ESTADO = ?, TOTAL = ? WHERE ID_MESA = ? AND FECHA_CIERRE IS NULL");
                // $consulta = self::consultasDB("UPDATE mesas SET FECHA_CIERRE = ?, ESTADO = ?, TOTAL = ? WHERE ID_MESA = ? AND FECHA_CIERRE = NULL");
                $fecha = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
                $fechaString = date_format($fecha, 'Y-m-d H:i:s');
                $total = $mesa->totalizarImporte();
                $consulta->bindParam(1, $fechaString);
                $consulta->bindParam(2, $estado);
                $consulta->bindParam(3, $total);
                $consulta->bindParam(4, $mesa->_idMesa);
                $consulta->execute();
                $consulta->closeCursor();
                break;
            default:
                $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET ESTADO = ? WHERE ID_MESA = ? AND FECHA_CIERRE IS NULL");
                // $consulta = self::consultasDB("UPDATE mesas SET ESTADO = ? WHERE ID_MESA = ? AND FECHA_CIERRE = NULL");
                $consulta->bindParam(1, $estado);
                $consulta->bindParam(2, $mesa->_idMesa);
                $consulta->execute();
                $consulta->closeCursor();
                break;
        }
        // $consulta->execute();
    }

    private function totalizarImporte()
    {
        $sumaImporte = 0;
        $arrayPedidos = Pedido::obtenerTodos($this->_idMesa);
        foreach($arrayPedidos as $pedido)
        {
            $sumaImporte += $pedido->_producto->_precio * $pedido->_cantidad;
        }
        return $sumaImporte;
    }

    // private static function consultasDB($sql)
    // {
    //     if(!isset(self::$objetoPDO))
    //     {
    //         try {
    //             $objetoPDO = new PDO('mysql:host=localhost;dbname=trabajo_practico_comanda;charset=utf8',"root","", array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    //             $objetoPDO->exec("SET CHARACTER SET utf8");
    //         } catch (PDOException $e) {
    //             print "Error: " . $e->getMessage();
    //             die();
    //         }

    //     }
    //     return $objetoPDO->prepare($sql);

    // }

}
?>