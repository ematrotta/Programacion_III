<?php

use Illuminate\Support\Facades\Process;
include_once "./db/AccesoDatos.php";
include_once "./Interfaces/ISector.php";

class Producto implements ISector{

    public $_idProducto;
    public $_titulo;
    public $_tiempoPreparacion;
    public $_precio;
    public $_estado;
    public $_sector;
    public $_fechaCreacion;

    
    const ESTADO_ACTIVO = "activo";
    const ESTADO_INACTIVO = "inactivo";
    const SECTOR_BAR = "bar";
    const SECTOR_CERVEZA = "cerveza";
    const SECTOR_MESA = "mesas";
    const SECTOR_COCINA = "cocina";
    const SECTOR_SOCIO = "socio";

    private static $objetoPDO;

    public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (TITULO,TIEMPO_PREPARACION,PRECIO,ESTADO,SECTOR,FECHA_CREACION) VALUES (?,?,?,?,?,?)");
        // $consulta = self::consultasDB("INSERT INTO productos (TITULO,TIEMPO_PREPARACION,PRECIO,ESTADO,SECTOR,FECHA_CREACION) VALUES (?,?,?,?,?,?)");
        $fecha = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $fechaString = date_format($fecha, 'Y-m-d H:i:s');
        $consulta->bindParam(1, $this->_titulo);
        $consulta->bindParam(2, $this->_tiempoPreparacion);
        $consulta->bindParam(3, $this->_precio);
        $consulta->bindParam(4, $this->_estado);
        $consulta->bindParam(5, $this->_sector);
        $consulta->bindParam(6, $fechaString);
        $consulta->execute();
        $consulta->closeCursor();

        return $objAccesoDatos->obtenerUltimoId();
        // return self::$objetoPDO->lastInsertId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos");
        // $consulta = self::consultasDB("SELECT * FROM productos");
        $consulta->execute();
        // $consulta->fetchAll(PDO::FETCH_OBJ);
        // return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
        $arrayProductos = array();
        $objetosObtenidos = $consulta->fetchAll(PDO::FETCH_OBJ);
        $consulta->closeCursor();

        foreach($objetosObtenidos as $prototipo)
        {
            array_push($arrayProductos,Producto::transformarPrototipo($prototipo));
        }
        // $consulta->closeCursor();

        return $arrayProductos;
    }

    public static function obtenerProducto($id)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE ID_PRODUCTO = ?");
        // $consulta = self::consultasDB("SELECT * FROM productos WHERE ID_PRODUCTO = ?");
        $consulta->bindParam(1, $id);
        $consulta->execute();

        // return $consulta->fetchObject('Producto');
        // return $consulta->fetch(PDO::FETCH_OBJ);
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        $consulta->closeCursor();
        if($prototipeObject != false)
        {
            $rtn = Producto::transformarPrototipo($prototipeObject);
        }
        // $consulta->closeCursor();

        return $rtn;
    }

    public static function obtenerProductoByName($nombreProducto)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE TITULO = ?");
        // $consulta = self::consultasDB("SELECT * FROM productos WHERE TITULO = ?");
        $consulta->bindParam(1, $nombreProducto);
        $consulta->execute();
 

        // return $consulta->fetchObject('Producto');
        // return $consulta->fetch(PDO::FETCH_OBJ);
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        $consulta->closeCursor();
        if($prototipeObject != false)
        {
            $rtn = Producto::transformarPrototipo($prototipeObject);
        }
        // $consulta->closeCursor();

        return $rtn;
    }

    private static function transformarPrototipo($prototipo)
    {   
        $producto = new Producto();
        $producto->_idProducto = $prototipo->ID_PRODUCTO;
        $producto->_titulo = $prototipo->TITULO;
        $producto->_tiempoPreparacion = $prototipo->TIEMPO_PREPARACION;
        $producto->_fechaCreacion = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_CREACION,new DateTimeZone("America/Argentina/Buenos_Aires"));
        $producto->_precio = $prototipo->PRECIO;
        $producto->_estado = $prototipo->ESTADO;
        $producto->_sector = $prototipo->SECTOR;
        return $producto;

    }

    public static function modificarProducto($producto)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET TITULO = ?, TIEMPO_PREPARACION = ?, PRECIO = ?, ESTADO = ?, SECTOR = ?  WHERE ID_PRODUCTO = ?");
        // $consulta = self::consultasDB("UPDATE productos SET TITULO = ?, TIEMPO_PREPARACION = ?, PRECIO = ?, ESTADO = ?, SECTOR = ?  WHERE ID_PRODUCTO = ?");
        $consulta->bindParam(1, $producto->_titulo);
        $consulta->bindParam(2, $producto->_tiempoPreparacion);
        $consulta->bindParam(3, $producto->_precio);
        $consulta->bindParam(4, $producto->_estado);
        $consulta->bindParam(5, $producto->_sector);
        $consulta->bindParam(6, $producto->_idProducto);
        $consulta->execute();
        $consulta->closeCursor();
    }

    public static function borrarProducto($producto)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM productos WHERE ID_PRODUCTO = ?");
        // $consulta = self::consultasDB("DELETE FROM productos WHERE ID_PRODUCTO = ?");
        $consulta->bindParam(1, $producto->_idProducto);
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