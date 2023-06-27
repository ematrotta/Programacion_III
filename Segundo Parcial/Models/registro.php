<?php

include_once "./db/AccesoDatos.php";

class Registro
{
    public $_usuario;
    public $_accion;
    public $_fechaAccion;
    public $_moneda;

    const ACCION_BORRAR_CRIPTO = "borrar cripto";

    public function crearRegistro()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO registro_acciones (ID_USUARIO,ID_CRIPTO,ACCION,FECHA) VALUES (?,?,?,?)");
        $strFecha = date_format($this->_fechaAccion, 'Y-m-d H:i:s');

        $consulta->bindParam(1, $this->_usuario->_idUsuario);
        $consulta->bindParam(2, $this->_moneda->_idMoneda);
        $consulta->bindParam(3, $this->_accion);
        $consulta->bindParam(4, $strFecha);

        $consulta->execute();

        $consulta->closeCursor();

        return $objAccesoDatos->obtenerUltimoId();
    }

}


?>