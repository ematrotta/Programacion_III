<?php

include_once "./db/AccesoDatos.php";

class Moneda
{
    public $_idMoneda;
    public $_nombre;
    public $_precio;
    public $_nacionalidad;
    public $_simbolo;
    public $_fechaAlta;
    public $_fechaBaja;

    public function crearMoneda()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO monedas (NOMBRE,SIMBOLO,NACIONALIDAD,PRECIO,FECHA_ALTA) VALUES (?,?,?,?,?)");
        $fechaAlta = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $strFechaAlta = date_format($fechaAlta, 'Y-m-d H:i:s');


        $consulta->bindParam(1, $this->_nombre);
        $consulta->bindParam(2, $this->_simbolo);
        $consulta->bindParam(3, $this->_nacionalidad);
        $consulta->bindParam(4, $this->_precio);
        $consulta->bindParam(5, $strFechaAlta);

        $consulta->execute();

        $consulta->closeCursor();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM monedas");
        $consulta->execute();

        $arrayMonedas = array();
        $objetosObtenidos = $consulta->fetchAll(PDO::FETCH_OBJ);
        $consulta->closeCursor();

        foreach($objetosObtenidos as $prototipo)
        {
            array_push($arrayMonedas,Moneda::transformarPrototipo($prototipo));
        }

        return $arrayMonedas;
    }

    public static function obtenerPorNacionalidad($nacionalidad)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM monedas WHERE NACIONALIDAD = ?");
        $consulta->bindParam(1,$nacionalidad);
        $consulta->execute();

        $arrayMonedas = array();
        $objetosObtenidos = $consulta->fetchAll(PDO::FETCH_OBJ);
        $consulta->closeCursor();

        foreach($objetosObtenidos as $prototipo)
        {
            array_push($arrayMonedas,Moneda::transformarPrototipo($prototipo));
        }

        return $arrayMonedas;
    }

    public static function obtenerMonedaById($idMoneda)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM monedas WHERE ID_MONEDA = :idMoneda");
        $consulta->bindValue(':idMoneda', $idMoneda,PDO::PARAM_INT);
        $consulta->execute();
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        $consulta->closeCursor();
        if($prototipeObject != false)
        {
            $rtn = Moneda::transformarPrototipo($prototipeObject);
        }
        return $rtn;
    }

    public static function obtenerMonedaBySimbol($simbolo)
    {
        $rtn = false;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM monedas WHERE SIMBOLO = :simbolo");
        $consulta->bindValue(':simbolo', $simbolo);
        $consulta->execute();
        $prototipeObject = $consulta->fetch(PDO::FETCH_OBJ);
        $consulta->closeCursor();
        if($prototipeObject != false)
        {
            $rtn = Moneda::transformarPrototipo($prototipeObject);
        }
        return $rtn;
    }

    private static function transformarPrototipo($prototipo)
    {   
        $moneda = new Moneda();
        $moneda->_idMoneda = $prototipo->ID_MONEDA;
        $moneda->_simbolo = $prototipo->SIMBOLO;
        $moneda->_fechaAlta = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_ALTA,new DateTimeZone("America/Argentina/Buenos_Aires"));
        if($prototipo->FECHA_BAJA != NULL)
        {
            $moneda->_fechaBaja = DateTime::createFromFormat('Y-m-d H:i:s',$prototipo->FECHA_BAJA,new DateTimeZone("America/Argentina/Buenos_Aires"));

        }
        else{
            $moneda->_fechaBaja = null;
        }
        $moneda->_nombre = $prototipo->NOMBRE;
        $moneda->_nacionalidad = $prototipo->NACIONALIDAD;
        $moneda->_precio = floatval($prototipo->PRECIO);
        return $moneda;

    }

    public static function modificarMoneda($moneda)
    {

        $rtn = false;
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE monedas SET NOMBRE = ?, NACIONALIDAD = ?, PRECIO = ? WHERE ID_MONEDA = ? AND FECHA_BAJA IS NULL");
        $consulta->bindParam(1, $moneda->_nombre);
        $consulta->bindValue(2, $moneda->_nacionalidad);
        $consulta->bindValue(3, $moneda->_precio);
        $consulta->bindValue(4, $moneda->_idMoneda);
        $consulta->execute();
        if($consulta->rowCount()>0)
        {
            $rtn = true;
        }
        $consulta->closeCursor();
        return $rtn;

    }

    public static function borrarMoneda($moneda)
    {
        $rtn = false;
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE monedas SET FECHA_BAJA = :fechaBaja WHERE ID_MONEDA = :idMoneda AND FECHA_BAJA IS NULL");
        $fechaBaja = new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires"));
        $strFechaBaja = date_format($fechaBaja, 'Y-m-d H:i:s');
        $consulta->bindValue(':idMoneda', $moneda->_idMoneda,PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja',$strFechaBaja);
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