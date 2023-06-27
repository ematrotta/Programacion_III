<?php
require_once './Models/Mesa.php';
require_once './Interfaces/IApiUsable.php';
require_once './Models/Usuario.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $nombreCliente = $parametros['nombreCliente'];
        // Falta agregar la foto y asociarla a la mesa
        $status = 200;
        $token = $request->getHeaderLine('Authorization');
        $usuario = AutentificadorJWT::ObtenerData($token);
        $nombreMozo = $usuario->_nombre;
        if(isset($nombreMozo) && $nombreMozo != "" && isset($nombreCliente) && $nombreCliente != "")
        {
          $nombreCliente = ucwords(strtolower($nombreCliente)," ");
          // Creamos el mesa
          $mesa = new Mesa();
          $mesa->_mozo = Usuario::obtenerUsuarioByName($nombreMozo);
          $mesa->_nombreCliente = $nombreCliente;

          if(($mesa->_mozo) != false)
          {
            $codigoMesa = $mesa->crearMesa();
            $payload = json_encode(array("mensaje" => "Mesa creada con exito","codigo"=>$codigoMesa));
          }
          else{
            $payload = json_encode(array("mensaje" => "Verifique haber ingresado el nombre de un mozo correcto"));
            $status = 404;
          }

        }
        else{
          $payload = json_encode(array("mensaje" => "No se ingresaron los datos correctos"));
          $status = 406;
        }
        $response->getBody()->write($payload);
        return $response
          ->withStatus($status)
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos mesa por nombre
        // $queryParams = $request->getQueryParams();
        // $id = $queryParams['idMesa'];
        
        $id = $args["mesa"];
        $status = 200;

        if(isset($id))
        {
          $mesa = Mesa::obtenerMesa($id);
          if($mesa != false)
          {

            $payload = json_encode($mesa);
          }
          else{
            $payload = json_encode(array("mensaje" => "No se encontró una mesa con ese ID"));
            $status = 404;

          }
        }
        else{
          $payload = json_encode(array("mensaje" => "No se colocó un ID"));
          $status = 406;
        }

        $response->getBody()->write($payload);
        return $response
          ->withStatus($status)
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      $lista = Mesa::obtenerTodos();
      $status = 200;

      $payload = json_encode(array("listaMesa" => $lista));
      if(!$lista)
      {
        // Al pasar status 204, no hay contenido directamente
        $payload = json_encode(array("mensaje" => "No se encontraron mesas"));
        $status = 204;
      }

      $response->getBody()->write($payload);
      return $response
        ->withStatus($status)
        ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $args['mesa'];
        $nombreCliente = $parametros['nombreCliente'];
        $estado = $parametros['estado'];
        $status = 200;

        $token = $request->getHeaderLine('Authorization');
        $usuario = AutentificadorJWT::ObtenerData($token);
        $tipoUsuario = $usuario->_tipo;
  
        if(isset($id) && isset($nombreCliente) && isset($estado))
        {
          $nombreCliente = ucwords(strtolower($nombreCliente)," ");
          $estado = strtolower($estado);
          $mesa = Mesa::obtenerMesa($id);
          if($mesa != false)
          {
            if($nombreCliente != "")
            {
              $mesa->_nombreCliente = $nombreCliente;
            }
            if($estado != "" && ($estado == Mesa::ESTADO_CANCELADO || $estado == Mesa::ESTADO_CERRADO || $estado == Mesa::ESTADO_COMIENDO || $estado == Mesa::ESTADO_ESPERANDO || $estado == Mesa::ESTADO_PAGO))
            {
              // Solo un socio puede cancelar o cerrar la mesa
              if($tipoUsuario != Usuario::SECTOR_SOCIO && ($estado == Mesa::ESTADO_CANCELADO || $estado == Mesa::ESTADO_CERRADO))
              {
                $payload = json_encode(array("mensaje" => "Acceso restringdo"));
                $status = 401;
              }

            }
            else{
              $payload = json_encode(array("mensaje" => "No se colocó el estado correcto"));
              $status = 406;
            }

            if($status == 200)
            {
              $mesa->_estado = $estado;
              Mesa::modificarEstado($mesa,$estado);
              Mesa::modificarMesa($mesa);
              $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));
            }


          }
          else{
            $status = 404;
            $payload = json_encode(array("mensaje" => "La mesa no fue encontrada"));
          }

        }
        else
        {
          $status = 406;
          $payload = json_encode(array("mensaje" => "Alguno de los datos es erroneo"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withStatus($status)
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarEstadoMesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $args['mesa'];
        $estado = $parametros['estado'];
        $status = 200;

        $token = $request->getHeaderLine('Authorization');
        $usuario = AutentificadorJWT::ObtenerData($token);
        $tipoUsuario = $usuario->_tipo;
  
        if(isset($id) && isset($estado))
        {
          $estado = strtolower($estado);
          $mesa = Mesa::obtenerMesa($id);
          if($mesa != false)
          {

            if($estado != "" && ($estado == Mesa::ESTADO_CANCELADO || $estado == Mesa::ESTADO_CERRADO || $estado == Mesa::ESTADO_COMIENDO || $estado == Mesa::ESTADO_ESPERANDO || $estado == Mesa::ESTADO_PAGO))
            {
              // Solo un socio puede cancelar o cerrar la mesa
              if($tipoUsuario != Usuario::SECTOR_SOCIO && ($estado == Mesa::ESTADO_CANCELADO || $estado == Mesa::ESTADO_CERRADO))
              {
                $payload = json_encode(array("mensaje" => "Acceso denegado"));
                $status = 401;
              }

            }
            else
            {
              $payload = json_encode(array("mensaje" => "No se colocó el estado correcto"));
              $status = 406;
            }

            if($status == 200)
            {
              $mesa->_estado = $estado;
              
              Mesa::modificarEstado($mesa,$estado);
              Mesa::modificarMesa($mesa);
              $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));
            }


          }
          else{
            $status = 404;
            $payload = json_encode(array("mensaje" => "La mesa no fue encontrada"));
          }

        }
        else
        {
          $status = 406;
          $payload = json_encode(array("mensaje" => "Alguno de los datos es erroneo"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withStatus($status)
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {

      // $parametros = $request->getParsedBody();
      $id = $args['mesa'];
      $status = 200;
      $mesa = Mesa::obtenerMesa($id);
      if(isset($id) && $mesa)
      {
        Mesa::modificarEstado($mesa,Mesa::ESTADO_CANCELADO);
        $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));

      }
      else{
        $status = 404;
        $payload = json_encode(array("mensaje" => "Mesa no encontrada"));
      }

    $response->getBody()->write($payload);
    return $response
      ->withStatus($status)
      ->withHeader('Content-Type', 'application/json');

    }

}

?>