<?php

include_once "./db/AccesoDatos.php";
include_once "./Models/Mesa.php";
include_once "./Models/Producto.php";
include_once "./Models/Usuario.php";

class Pedido{

    public $_idPedido;
    public $_mesa;
    public $_usuarioAsignado;
    public $_producto;
    public $_cantidad;
    public $_fechaEstimadaDeFinalizacion;
    public $_fechaFinalizacion;
    public $_sector;
    public $_estado;
    
    const ESTADO_PENDIENTE = "pendiente";
    const ESTADO_PREPARACION = "en preparacion";
    const ESTADO_LISTO = "listo para servir";
    const ESTADO_CANCELADO = "cancelado";

    private static $objetoPDO;

    public function crearPedido()
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (ID_MESA,ID_PRODUCTO,CANTIDAD,FECHA_ESTIMADA_FINALIZACION,SECTOR,ESTADO) VALUES (?,?,?,?,?,?)");
        // $consulta = self::consultasDB("INSERT INTO pedidos (ID_MESA,ID_PRODUCTO,CANTIDAD,FECHA_ESTIMADA_FINALIZACION,SECTOR,ESTADO) VALUES (?,?,?,?,?,?)");
        // Calulo la fecha de finalizacion estimada del pedido
        $tiempoEnMinutosDeProducto = $this->_producto->_tiempoPreparacion;
        $interval = DateInterval::createFromDateString($tiempoEnMinutosDeProducto.'minutes');
        $fechaEstimadaFinalizacion = $this->_mesa->_fechaApertura->add($interval);

        // Se cargan los datos faltantes en el pedido
        $this->_fechaEstimadaDeFinalizacion = $fechaEstimadaFinalizacion;
        $this->_sector = $this->_producto->_sector;
        $this->_estado = self::ESTADO_PENDIENTE;

        $fechaEstimadaFinalizacionString = date_format($this->_fechaEstimadaDeFinalizacion, 'Y-m-d H:i:s');

        
        $consulta->bindParam(1, $this->_mesa->_idMesa);
        $consulta->bindParam(2, $this->_producto->_idProducto);
        $consulta->bindParam(3, $this->_cantidad);
        $consulta->bindParam(4, $fechaEstimadaFinalizacionString);
        $consulta->bindParam(5, $this->_sector);
        $consulta->bindParam(6, $this->_estado);
        $consulta->execute();
        $consulta->closeCursor();

        return $objAccesoDatos->obtenerUltimoId();
        // return self::$objetoPDO->lastInsertId();
    }

    public static function obtenerTodos($idMesa = NULL)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        if($idMesa == null)
        {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
            // $consulta = self::consultasDB("SELECT * FROM pedidos");
            
        }
        else{

            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE ID_MESA = ?");
            // $consulta = self::consultasDB("SELECT * FROM pedidos WHERE ID_MESA = ?");
            $consulta->bindParam(1, $idMesa);
        }
        
        $consulta->execute();
        
        // $consulta->fetchAll(PDO::FETCH_OBJ);
        // return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        $arrayPedidos = array();
        $objetosObtenidos = $consulta->fetchAll(PDO::FETCH_OBJ);
        $consulta->closeCursor();

        foreach($objetosObtenidos as $prototipo)
        {
            array_push($arrayPedidos,Pedido::transformarPrototipo($prototipo));
        }
        // $consulta->closeCursor();

        return $arrayPedidos;
    }

    public static function obtenerPorSectorOEstado($sector = null,$estado = null)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        if($sector == Usuario::SECTOR_MESA || $sector == Usuario::SECTOR_SOCIO)
        {
            // Si el sector es mozo, se obtienen solo los pedidos que cumplan con el estado solicitado
            $sector = null;
        }
        if($sector == null && $estado != null)
        {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE ESTADO = ?");
            $consulta->bindParam(1, $estado);
            // $consulta = self::consultasDB("SELECT * FROM pedidos");
            
        }
        else{

            if($sector != null && $estado == null)
            {
                $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE SECTOR = ?");
                // $consulta = self::consultasDB("SELECT * FROM pedidos WHERE ID_MESA = ?");
                $consulta->bindParam(1, $sector);

            }
            else{
                
                $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE SECTOR = ? AND ESTADO = ?");
                // $consulta = self::consultasDB("SELECT * FROM pedidos WHERE ID_MESA = ?");
                $consulta->bindParam(1, $sector);
                $consulta->bindParam(2, $estado);
                
            }


        }
        
        $consulta->execute();
        
        // $consulta->fetchAll(PDO::FETCH_OBJ);
        // return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        $arrayPedidos = array();
        $objetosObtenidos = $consulta->fetchAll(PDO::FETCH_OBJ);
        $consulta->closeCursor();

        foreach($objetosObtenidos as $prototipo)
        {
            array_push($arrayPedidos,Pedido::transformarPrototipo($prototipo));
        }
        // $consulta->closeCursor();

        return $arrayPedidos;
    }

    public static function obtenerPedido($id)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE ID_PEDIDO = ?");
        // $consulta = self::consultasDB("SELECT * FROM pedidos WHERE ID_PEDIDO = ?");
        $consulta->bindParam(1, $id);
        $consulta->execute();

        // return $consulta->fetchObject('Pedido');
        // return $consulta->fetch(PDO::FETCH_OBJ);
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        $consulta->closeCursor();
        if($prototipeObject != false)
        {
            $rtn = Pedido::transformarPrototipo($prototipeObject);
        }
        // $consulta->closeCursor();

        return $rtn;
    }

    private static function transformarPrototipo($prototipo)
    {   
        $pedido = new Pedido();
        $pedido->_idPedido = $prototipo->ID_PEDIDO;
        $pedido->_mesa = Mesa::obtenerMesa($prototipo->ID_MESA);
        $pedido->_usuarioAsignado = Usuario::obtenerUsuario($prototipo->ID_USUARIO);
        $pedido->_producto = Producto::obtenerProducto($prototipo->ID_PRODUCTO);
        $pedido->_cantidad = $prototipo->CANTIDAD;
        $pedido->_fechaEstimadaDeFinalizacion = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_ESTIMADA_FINALIZACION,new DateTimeZone("America/Argentina/Buenos_Aires"));
        if($prototipo->FECHA_FINALIZACION != NULL)
        {
            $pedido->_fechaFinalizacion = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_FINALIZACION,new DateTimeZone("America/Argentina/Buenos_Aires"));

        }
        else{
            $pedido->_fechaFinalizacion = $prototipo->FECHA_FINALIZACION;
        }
        $pedido->_sector = $prototipo->SECTOR;
        $pedido->_estado = $prototipo->ESTADO;
        return $pedido;
    }

    public static function modificarPedido($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET ID_USUARIO = ?, ID_PRODUCTO = ?, CANTIDAD = ?, FECHA_ESTIMADA_FINALIZACION = ?, SECTOR = ?, ESTADO = ? WHERE ID_PEDIDO = ?");
        // Calulo la fecha de finalizacion estimada del pedido
        // $consulta = self::consultasDB("UPDATE pedidos SET ID_USUARIO = ?, ID_PRODUCTO = ?, CANTIDAD = ?, FECHA_ESTIMADA_FINALIZACION = ?, SECTOR = ?, ESTADO = ? WHERE ID_PEDIDO = ?");
        $tiempoEnMinutosDeProducto = $pedido->_cantidad*$pedido->_producto->_tiempoPreparacion;
        $interval = DateInterval::createFromDateString($tiempoEnMinutosDeProducto.'minutes');
        $fechaEstimadaFinalizacion = $pedido->_mesa->_fechaApertura->add($interval);
        $fechaEstimadaFinalizacionString = date_format($fechaEstimadaFinalizacion, 'Y-m-d H:i:s');
        
        $consulta->bindParam(1, $pedido->_usuarioAsignado->_idUsuario);
        $consulta->bindParam(2, $pedido->_producto->_idProducto);
        $consulta->bindParam(3, $pedido->_cantidad);
        $consulta->bindParam(4, $fechaEstimadaFinalizacionString);
        $consulta->bindParam(5, $pedido->_producto->_sector);
        $consulta->bindParam(6, $pedido->_estado);
        $consulta->bindParam(7, $pedido->_idPedido);

        $consulta->execute();
        $consulta->closeCursor();
    }

    public static function modificarEstado($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        if($pedido->_estado == self::ESTADO_PREPARACION)
        {
            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET ID_USUARIO = ?, ESTADO = ? WHERE ID_PEDIDO = ? AND FECHA_FINALIZACION IS NULL");
            $consulta->bindParam(1, $pedido->_usuarioAsignado->_idUsuario);
            $consulta->bindParam(2, $pedido->_estado);
            $consulta->bindParam(3, $pedido->_idPedido);

        }
        else
        {
            $fechaFinalizacion = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
            $strFechaFinalizacion = date_format($fechaFinalizacion, 'Y-m-d H:i:s');
            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET FECHA_FINALIZACION = ?, ESTADO = ? WHERE ID_PEDIDO = ? AND FECHA_FINALIZACION IS NULL");
            $consulta->bindParam(1, $strFechaFinalizacion);
            $consulta->bindParam(2, $pedido->_estado);
            $consulta->bindParam(3, $pedido->_idPedido);
        }

        $consulta->execute();
        $consulta->closeCursor();
    }
    public static function ActualizarFechaFinalizacion($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET FECHA_FINALIZACION = ? WHERE ID_PEDIDO = ?");
        $fechaFinalizacion = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $strFechaFinalizacion = date_format($fechaFinalizacion, 'Y-m-d H:i:s');
        $consulta->bindParam(1, $strFechaFinalizacion);
        $consulta->bindParam(2, $pedido->_idPedido);
        $consulta->execute();
        $consulta->closeCursor();
    }

    public static function borrarPedido($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $estado = self::ESTADO_CANCELADO;
        
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET ESTADO = ? AND FECHA_FINALIZACION = ? WHERE ID_PEDIDO = ?");
        // $consulta = self::consultasDB("UPDATE pedidos SET ESTADO = ? WHERE ID_PEDIDO = ?");
        $consulta->bindParam(1, $estado);
        $consulta->bindParam(2, $pedido->_fechaFinalizacion);
        $consulta->bindParam(3, $pedido->_idPedido);
        $consulta->execute();
        $consulta->closeCursor();
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