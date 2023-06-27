<?php

class Encuesta{

    public $_idEncuesta;
    public $_mesa;
    public $_puntuacionMozo;
    public $_puntuacionRestaurante;
    public $_puntuacionMesa;
    public $_puntuacionCocinero;
    public $_comentario;
    public $_fechaEncuesta;

    public function __construct($idEncuesta,$mesa,$puntuacionMozo,$puntuacionRestaurante,$puntuacionMesa,$puntuacionCocinero,$comentario,$fechaEncuesta) {
        $this->_idEncuesta = $idEncuesta;
        $this->_mesa = $mesa;
        $this->_puntuacionMozo = $puntuacionMozo;
        $this->_puntuacionRestaurante = $puntuacionRestaurante;
        $this->_puntuacionMesa = $puntuacionMesa;
        $this->_puntuacionCocinero = $puntuacionCocinero;
        $this->_comentario = $comentario;
        $this->_fechaEncuesta = $fechaEncuesta;
    }


    


}
?>