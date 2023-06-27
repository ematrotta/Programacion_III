<?php

include_once "./db/AccesoDatos.php";

class Venta
{
    public $_idVenta;
    public $_moneda;
    public $_usuario;
    public $_precio;
    public $_cantidad;
    public $_total;
    public $_fechaVenta;
    public $_estado;

    const ESTADO_CERRADA = "cerrada";
    const ESTADO_CANCELADA = "cancelada";

    public function crearVenta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ventas (ID_MONEDA,ID_USUARIO,PRECIO,CANTIDAD,TOTAL,FECHA,ESTADO) VALUES (?,?,?,?,?,?,?)");
        $fechaAlta = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $strFechaAlta = date_format($fechaAlta, 'Y-m-d H:i:s');
        $total = $this->_moneda->_precio*$this->_cantidad;
        $estado = self::ESTADO_CERRADA;

        $consulta->bindParam(1, $this->_moneda->_idMoneda);
        $consulta->bindParam(2, $this->_usuario->_idUsuario);
        $consulta->bindParam(3, $this->_moneda->_precio);
        $consulta->bindParam(4, $this->_cantidad);
        $consulta->bindParam(5, $total);
        $consulta->bindParam(6, $strFechaAlta);
        $consulta->bindParam(7, $estado);
        $consulta->execute();
        $consulta->closeCursor();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ventas");
        $consulta->execute();

        $arrayVentas = array();
        $objetosObtenidos = $consulta->fetchAll(PDO::FETCH_OBJ);
        $consulta->closeCursor();

        foreach($objetosObtenidos as $prototipo)
        {
            array_push($arrayVentas,Venta::transformarPrototipo($prototipo));
        }

        return $arrayVentas;
    }

    public static function obtenerTodosTipoMoneda($moneda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ventas WHERE ID_MONEDA = ?");
        $consulta->bindParam(1,$moneda->_idMoneda);
        $consulta->execute();

        $arrayVentas = array();
        $objetosObtenidos = $consulta->fetchAll(PDO::FETCH_OBJ);
        $consulta->closeCursor();

        foreach($objetosObtenidos as $prototipo)
        {
            array_push($arrayVentas,Venta::transformarPrototipo($prototipo));
        }

        return $arrayVentas;
    }


    public static function obtenerVentaById($idVenta)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ventas WHERE ID_VENTA = :idVenta");
        $consulta->bindValue(':idVenta', $idVenta,PDO::PARAM_INT);
        $consulta->execute();
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        $consulta->closeCursor();
        if($prototipeObject != false)
        {
            $rtn = Venta::transformarPrototipo($prototipeObject);
        }
        return $rtn;
    }

    private static function transformarPrototipo($prototipo)
    {   
        $venta = new Venta();
        $venta->_idVenta = $prototipo->ID_VENTA;
        $venta->_usuario = Usuario::obtenerUsuarioById($prototipo->ID_USUARIO);
        $venta->_moneda = Moneda::obtenerMonedaById($prototipo->ID_MONEDA);
        $venta->_fechaVenta = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA,new DateTimeZone("America/Argentina/Buenos_Aires"));
        $venta->_precio = intval($prototipo->CANTIDAD);
        $venta->_precio = floatval($prototipo->PRECIO);
        $venta->_total = floatval($prototipo->TOTAL);
        $venta->_estado = $prototipo->ESTADO;
        return $venta;

    }

    public static function modificarVenta($venta)
    {

        $rtn = false;
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ventas SET ID_MONEDA = ?, CANTIDAD = ?, PRECIO = ?, TOTAL = ?, ESTADO = ? WHERE ID_VENTA = ? AND ESTADO <> ?");
        $total = $venta->_moneda->_precio*$venta->_cantidad;
        $estadoCancelada = self::ESTADO_CANCELADA;

        $consulta->bindParam(1, $venta->_moneda->_idMoneda);
        $consulta->bindParam(2, $venta->_cantidad);
        $consulta->bindParam(3, $venta->_moneda->_precio);
        $consulta->bindParam(4, $total);
        $consulta->bindParam(5, $venta->_estado);
        $consulta->bindParam(6, $venta->_idVenta);
        $consulta->bindParam(7, $estadoCancelada);

        $consulta->execute();
        if($consulta->rowCount()>0)
        {
            $rtn = true;
        }
        $consulta->closeCursor();
        return $rtn;

    }

    public static function borrarVenta($venta)
    {
        $rtn = false;
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ventas SET ESTADO = :estado WHERE ID_VENTA = :idVenta AND ESTADO <> :estadoCancelado");
        $estadoCancelada = self::ESTADO_CANCELADA;
        $consulta->bindValue(':idVenta', $venta->_idVenta,PDO::PARAM_INT);
        $consulta->bindValue(':estado',$estadoCancelada);
        $consulta->bindValue(':estadoCancelado',$estadoCancelada);
        $consulta->execute();
        if($consulta->rowCount()>0)
        {
            $rtn = true;
        }
        $consulta->closeCursor();
        return $rtn;
    }

}


?>