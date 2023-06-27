<?php

include_once "./db/AccesoDatos.php";

class Usuario
{
    public $_idUsuario;
    public $_mail;
    public $_nombre;
    public $_password;
    public $_tipo;
    public $_fechaAlta;
    public $_fechaBaja;

    const TIPO_ADMIN = "admin";
    const TIPO_CLIENTE = "cliente";

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (NOMBRE,EMAIL,PASSWORD,TIPO,FECHA_ALTA) VALUES (:nombre,:email,:clave,:tipo,:fechaAlta)");
        $claveHash = password_hash($this->_password, PASSWORD_DEFAULT);
        $fechaAlta = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $strFechaAlta = date_format($fechaAlta, 'Y-m-d H:i:s');

        $consulta->bindValue(':nombre', $this->_nombre, PDO::PARAM_STR);
        $consulta->bindValue(':email', $this->_mail, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':tipo', $this->_tipo, PDO::PARAM_STR);
        $consulta->bindValue(':fechaAlta', $strFechaAlta);
        $consulta->execute();

        $consulta->closeCursor();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios");
        $consulta->execute();

        $arrayUsuarios = array();
        $objetosObtenidos = $consulta->fetchAll(PDO::FETCH_OBJ);
        $consulta->closeCursor();

        foreach($objetosObtenidos as $prototipo)
        {
            array_push($arrayUsuarios,Usuario::transformarPrototipo($prototipo));
        }

        return $arrayUsuarios;
    }

    public static function obtenerUsuarioById($idUsuario)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE ID_USUARIO = :idUsuario");
        $consulta->bindValue(':idUsuario', $idUsuario,PDO::PARAM_INT);
        $consulta->execute();
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        $consulta->closeCursor();
        if($prototipeObject != false)
        {
            $rtn = Usuario::transformarPrototipo($prototipeObject);
        }
        return $rtn;
    }

    private static function transformarPrototipo($prototipo)
    {   
        $usuario = new Usuario();
        $usuario->_idUsuario = $prototipo->ID_USUARIO;
        $usuario->_mail = $prototipo->EMAIL;
        $usuario->_fechaAlta = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_ALTA,new DateTimeZone("America/Argentina/Buenos_Aires"));
        if($prototipo->FECHA_BAJA != NULL)
        {
            $usuario->_fechaBaja = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_BAJA,new DateTimeZone("America/Argentina/Buenos_Aires"));

        }
        else{
            $usuario->_fechaBaja = null;
        }
        $usuario->_nombre = $prototipo->NOMBRE;
        $usuario->_password = $prototipo->PASSWORD;
        $usuario->_tipo = $prototipo->TIPO;
        return $usuario;

    }

    public static function modificarUsuario($usuario)
    {

        $rtn = false;
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET NOMBRE = :nombre, EMAIL = :email, TIPO = :tipo, PASSWORD = :password WHERE ID_USUARIO = :idUsuario AND FECHA_BAJA IS NULL");
        $claveHash = password_hash($usuario->_password, PASSWORD_DEFAULT);
        $consulta->bindValue(':nombre', $usuario->_nombre);
        $consulta->bindValue(':password', $claveHash);
        $consulta->bindValue(':idUsuario', $usuario->_idUsuario,PDO::PARAM_INT);
        $consulta->bindValue(':email', $usuario->_mail);
        $consulta->bindValue(':tipo', $usuario->_tipo);
        $consulta->execute();
        if($consulta->rowCount()>0)
        {
            $rtn = true;
        }
        $consulta->closeCursor();
        return $rtn;

    }

    public static function borrarUsuario($usuario)
    {
        $rtn = false;
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET FECHA_BAJA = :fechaBaja WHERE ID_USUARIO = :idUsuario AND FECHA_BAJA IS NULL");
        $fechaBaja = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $strFechaBaja = date_format($fechaBaja, 'Y-m-d H:i:s');
        $consulta->bindValue(':idUsuario', $usuario->_idUsuario,PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja',$strFechaBaja);
        $consulta->execute();
        if($consulta->rowCount()>0)
        {
            $rtn = true;
        }
        $consulta->closeCursor();
        return $rtn;
    }

    public function insertarRegistroIngreso()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO registro_ingresos (FECHA_INGRESO,ID_USUARIO_INGRESADO) VALUES (?,?)");
        $fecha = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $fechaString = date_format($fecha, 'Y-m-d H:i:s');
        $consulta->bindParam(1, $fechaString);
        $consulta->bindParam(2, $this->_idUsuario);
        $consulta->execute();
        $consulta->closeCursor();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function validarUsuarioExistente($mailUsuario)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE EMAIL = ?");
        $consulta->bindParam(1, $mailUsuario);
        $consulta->execute();
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        $consulta->closeCursor();
        if($prototipeObject != false)
        {
            $rtn = true;
        }

        return $rtn;
    }

    public static function validarUsuario($mail,$password)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE EMAIL = ? AND FECHA_BAJA IS NULL");
        $consulta->bindParam(1, $mail);
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
}


?>