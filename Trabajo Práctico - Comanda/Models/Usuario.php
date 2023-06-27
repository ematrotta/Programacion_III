<?php

include_once "./db/AccesoDatos.php";
include_once "./Interfaces/ISector.php";

class Usuario implements ISector{

    public $_idUsuario;
    public $_nombre;
    public $_fechaCreacion;
    public $_fechaFinalizacion;
    public $_user;
    public $_password;
    public $_tipo;

    const SECTOR_BAR = "bar";
    const SECTOR_CERVEZA = "cerveza";
    const SECTOR_MESA = "mesas";
    const SECTOR_COCINA = "cocina";
    const SECTOR_SOCIO = "socio";

    private static $objetoPDO;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (NOMBRE,FECHA_CREACION,USER,PASSWORD,TIPO) VALUES (?,?,?,?,?)");
        // $consulta = self::consultasDB("INSERT INTO usuarios (NOMBRE, FECHA_CREACION,USER,PASSWORD,TIPO) VALUES (?,?,?,?,?,?)");
        $fecha = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $claveHash = password_hash($this->_password, PASSWORD_DEFAULT);
        $fechaString = date_format($fecha, 'Y-m-d H:i:s');
        $consulta->bindParam(1, $this->_nombre);
        $consulta->bindParam(2, $fechaString);
        $consulta->bindParam(3, $this->_user);
        $consulta->bindParam(4, $claveHash);
        $consulta->bindParam(5, $this->_tipo);
        $consulta->execute();
        $consulta->closeCursor();

        return $objAccesoDatos->obtenerUltimoId();
        // return self::$objetoPDO->lastInsertId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios");
        // $consulta = self::consultasDB("SELECT * FROM usuarios");
        $consulta->execute();
        
        // $consulta->fetchAll(PDO::FETCH_OBJ);
        // return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        $arrayUsuarios = array();
        $usuariosObtenidos = $consulta->fetchAll(PDO::FETCH_OBJ);
        $consulta->closeCursor();

        foreach($usuariosObtenidos as $prototipo)
        {
            array_push($arrayUsuarios,Usuario::transformarPrototipo($prototipo));
        }
        // $consulta->closeCursor();
        

        return $arrayUsuarios;
    }

    public static function obtenerUsuario($id)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE ID_USUARIO = ?");
        // $consulta = self::consultasDB("SELECT * FROM usuarios WHERE ID_USUARIO = ?");
        $consulta->bindParam(1, $id);
        $consulta->execute();
        
        // return $consulta->fetchObject('Usuario');
        // return $consulta->fetch(PDO::FETCH_OBJ);
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        $consulta->closeCursor();

        if($prototipeObject != false)
        {
            $rtn = Usuario::transformarPrototipo($prototipeObject);
        }
        // $consulta->closeCursor();

        return $rtn;
    }

    public static function obtenerUsuarioByName($nombreUsuario)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE NOMBRE = ?");
        // $consulta = self::consultasDB("SELECT * FROM usuarios WHERE NOMBRE = ?");
        $consulta->bindParam(1, $nombreUsuario);
        $consulta->execute();
        

        // return $consulta->fetchObject('Usuario');
        // return $consulta->fetch(PDO::FETCH_OBJ);
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        $consulta->closeCursor();
        if($prototipeObject != false)
        {
            $rtn = Usuario::transformarPrototipo($prototipeObject);
        }
        // $consulta->closeCursor();

        return $rtn;
    }

    public static function validarUsuario($user,$password)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE USER = ?");
        $consulta->bindParam(1, $user);
        $consulta->execute();
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        $consulta->closeCursor();
        if($prototipeObject != false)
        {
            $usuario = Usuario::transformarPrototipo($prototipeObject);
            if(password_verify($password,$usuario->_password))
            {
                $rtn = $usuario;
            }
        }

        return $rtn;
    }

    private static function transformarPrototipo($prototipo)
    {   
        $usuario = new Usuario();
        $usuario->_idUsuario = $prototipo->ID_USUARIO;
        $usuario->_nombre = $prototipo->NOMBRE;
        $usuario->_fechaCreacion = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_CREACION,new DateTimeZone("America/Argentina/Buenos_Aires"));
        if($prototipo->FECHA_BAJA != NULL)
        {
            $usuario->_fechaFinalizacion = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_BAJA,new DateTimeZone("America/Argentina/Buenos_Aires"));

        }
        else{
            $usuario->_fechaFinalizacion = $prototipo->FECHA_BAJA;
        }
        $usuario->_user = $prototipo->USER;
        $usuario->_password = $prototipo->PASSWORD;
        $usuario->_tipo = $prototipo->TIPO;
        return $usuario;

    }

    public static function modificarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET NOMBRE = ?, USER = ?, PASSWORD = ?, TIPO = ?  WHERE ID_USUARIO = ?");
        // $consulta = self::consultasDB("UPDATE usuarios SET NOMBRE = ?, USER = ?, PASSWORD = ?, TIPO = ?  WHERE ID_USUARIO = ?");
        $claveHash = password_hash($usuario->_password, PASSWORD_DEFAULT);
        $consulta->bindParam(1, $usuario->_nombre);
        $consulta->bindParam(2, $usuario->_user);
        $consulta->bindParam(3, $claveHash);
        $consulta->bindParam(4, $usuario->_tipo);
        $consulta->bindParam(5, $usuario->_idUsuario);
        $consulta->execute();
        $consulta->closeCursor();
    }

    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET FECHA_BAJA = ? WHERE ID_USUARIO = ?");
        // $consulta = self::consultasDB("UPDATE usuarios SET FECHA_BAJA = ? WHERE ID_USUARIO = ?");
        $fecha = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $fechaString = date_format($fecha, 'Y-m-d H:i:s');
        $consulta->bindParam(1, $fechaString);
        $consulta->bindParam(2, $usuario->_idUsuario);
        $consulta->execute();
        $consulta->closeCursor();
    }

    public function insertarRegistroIngreso()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO registro_ingresos (FECHA_INGRESO,ID_USUARIO_INGRESADO) VALUES (?,?)");
        // $consulta = self::consultasDB("INSERT INTO usuarios (NOMBRE, FECHA_CREACION,USER,PASSWORD,TIPO) VALUES (?,?,?,?,?,?)");
        $fecha = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $fechaString = date_format($fecha, 'Y-m-d H:i:s');
        $consulta->bindParam(1, $fechaString);
        $consulta->bindParam(2, $this->_idUsuario);
        $consulta->execute();
        $consulta->closeCursor();

        return $objAccesoDatos->obtenerUltimoId();
        // return self::$objetoPDO->lastInsertId();
    }

    public static function validarUsuarioExistente($user)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE USER = ?");
        $consulta->bindParam(1, $user);
        $consulta->execute();
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        $consulta->closeCursor();
        if($prototipeObject != false)
        {
            $rtn = true;
        }

        return $rtn;
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