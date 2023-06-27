<?php

use Illuminate\Support\Facades\Process;

include_once './Models/Pedido.php';
include_once "./Models/Producto.php";
include_once "./Models/Usuario.php";
require_once './Interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $status = 200;

      $idMesa = $parametros['idMesa'];
      $nombreProducto = $parametros['nombreProducto'];
      $cantidad = $parametros['cantidad'];

      if(isset($idMesa) && $idMesa != "" && isset($nombreProducto) && $nombreProducto != "")
      {
        $nombreProducto = ucwords(strtolower($nombreProducto)," ");

        // Creamos el pedido
        $pedido = new Pedido();
        $pedido->_mesa = Mesa::obtenerMesa($idMesa);

        $pedido->_producto = Producto::obtenerProductoByName($nombreProducto);
        $pedido->_cantidad = (int)$cantidad;
        if($pedido->_mesa != false && $pedido->_cantidad>0 && $pedido->_producto != false)
        {
          $pedido->crearPedido();
          $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

        }
        else{
          $payload = json_encode(array("mensaje" => "Verifique que haya ingresado correctamente el id de la mesa o el producto"));
          $status = 204;

        }

      }
      else{
        $payload = json_encode(array("mensaje" => "Verifique haber ingresado todos los datos"));
        $status = 406;

      }

        $response->getBody()->write($payload);
        return $response
          ->withStatus($status)
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos pedido por nombre
        // $queryParams = $request->getQueryParams();
        // $id = $queryParams['idPedido'];
        $id = $args["pedido"];
        $status = 200;
        if(isset($id))
        {
          $pedido = Pedido::obtenerPedido($id);
          if($pedido)
          {
            $payload = json_encode($pedido);
          }
          else{
            $status = 204;
            $payload = json_encode(array("mensaje" => "No se encontró un pedido con ese ID"));

          }
          
        }
        else{
          $status = 406;
          $payload = json_encode(array("mensaje" => "Verifique haber ingresado un ID"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withStatus($status)
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      $params = $request->getQueryParams();
      $estado = $params["estado"];

      $lista = Pedido::obtenerTodos();
      if(isset($estado))
      {
        $lista = Pedido::obtenerPorSectorOEstado(null,$estado);

      }
      $status = 200;
      if(!$lista)
      {
        $status = 204;
        $payload = json_encode(array("Mensaje" => "No hay pedidos"));
      }
      $payload = json_encode(array("listaPedido" => $lista));
      $response->getBody()->write($payload);
      return $response
        ->withStatus($status)
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPendientes($request, $response, $args)
    {
      $token = $request->getHeaderLine('Authorization');
      $usuario = AutentificadorJWT::ObtenerData($token);
      $tipoUsuario = $usuario->_tipo;

      $lista = Pedido::obtenerPorSectorOEstado($tipoUsuario,Pedido::ESTADO_PENDIENTE);
      $status = 200;
      if(!$lista)
      {
        $status = 204;
      }
      $payload = json_encode(array("listaPedido" => $lista));
      $response->getBody()->write($payload);
      return $response
        ->withStatus($status)
        ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $status = 200;
        $parametros = $request->getParsedBody();
        $token = $request->getHeaderLine('Authorization');

        $usuario = AutentificadorJWT::ObtenerData($token);
        $nombreEmpleado = $usuario->_nombre;
        $tipoUsuario = $usuario->_tipo;

        $id = $args['pedido'];
        $nombreProducto = $parametros['nombreProducto'];
        $cantidad = $parametros['cantidad'];
        $estado = $parametros["estado"];

        if(isset($id) && isset($nombreEmpleado) && isset($nombreProducto) && isset($cantidad) && isset($estado))
        {
          $pedido = Pedido::obtenerPedido($id);
          $cantidad = intval($cantidad);
          $estado = strtolower($estado);

          if($pedido)
          {
            if($cantidad>0 && $pedido->_sector == $tipoUsuario)
            {
              $nombreProducto = ucwords(strtolower($nombreProducto)," ");
              $estado = strtolower($estado);
              
              if($nombreEmpleado != "")
              {
                $empleado = Usuario::obtenerUsuarioByName($nombreEmpleado);
                if($empleado)
                {
                  $pedido->_usuarioAsignado = $empleado;
                }
                
              }
              if($nombreProducto != "")
              {
                $producto = Producto::obtenerProductoByName($nombreProducto);
                if($producto)
                {
                  $pedido->_producto = $producto;
                }
                
              }
              if($cantidad>0)
              {
                $pedido->_cantidad = $cantidad;
              }
              if($estado != "" && ($estado == Pedido::ESTADO_PREPARACION || $estado == Pedido::ESTADO_LISTO))
              {
                $pedido->_estado = $estado;
                if($estado == Pedido::ESTADO_LISTO)
                {
                  Pedido::ActualizarFechaFinalizacion($pedido);
                }
              }
              Pedido::modificarPedido($pedido);
              $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

            }
            else
            {
              $status = 401;
              $payload = json_encode(array("mensaje" => "No se puede realizar la acción solicitada. Verifique que el pedido corresponda al sector o bien haber ingresado una cantidad adecuada"));
            }
 

          }
          else
          {
            // $status = 204;
            $payload = json_encode(array("mensaje" => "No se encontró el pedido"));
          }

        }
        else{
          $status = 406;
          $payload = json_encode(array("mensaje" => "Aseguro de haber colocado las variables necesarias"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withStatus($status)
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarEstadoPedido($request, $response, $args)
    {
        $status = 200;
        $parametros = $request->getParsedBody();
        $token = $request->getHeaderLine('Authorization');

        $usuarioToken = AutentificadorJWT::ObtenerData($token);
        $usuario = Usuario::obtenerUsuario($usuarioToken->_idUsuario);
        $tipoUsuario = $usuario->_tipo;

        $id = $args['pedido'];
        $estado = $parametros["estado"];

        if(isset($id) && isset($estado) )
        {
          $estado = strtolower($estado);
          $pedido = Pedido::obtenerPedido($id);

          if($pedido && ($estado == Pedido::ESTADO_LISTO || $estado == Pedido::ESTADO_PREPARACION) && $pedido->_fechaFinalizacion == null)
          {
            if($pedido->_sector == $tipoUsuario || $tipoUsuario == Usuario::SECTOR_SOCIO)
            {

              if($pedido->_usuarioAsignado == null)
              {
                if($estado == Pedido::ESTADO_PREPARACION)
                {
                  $pedido->_usuarioAsignado = $usuario;
                }
                else{
                  $status = 401;
                }

              }
              else{
                if($estado != Pedido::ESTADO_LISTO || $pedido->_usuarioAsignado->_idUsuario != $usuario->_idUsuario)
                {
                  $status = 401;
                }
              }

              if($status == 200)
              {
                $pedido->_estado = $estado;
                Pedido::modificarEstado($pedido);
                $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
              }
              else{
                $payload = json_encode(array("mensaje" => "Acceso denegado"));

              }
            }
            else
            {
              $status = 401;
              $payload = json_encode(array("mensaje" => "No se puede realizar la acción solicitada. Verifique que el pedido corresponda a su sector"));
            }
 

          }
          else
          {
            $status = 404;
            $payload = json_encode(array("mensaje" => "No se encontró el pedido o colocó el estado del pedido erroneamente"));
          }

        }
        else{
          $status = 406;
          $payload = json_encode(array("mensaje" => "Asegurese de haber colocado las variables necesarias"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withStatus($status)
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        // $parametros = $request->getParsedBody();
        $id = $args['pedido'];
        $status = 200;
        if(isset($id))
        {
          $pedido = Pedido::obtenerPedido($id);
          if($pedido)
          {
            Pedido::borrarPedido($pedido);
            $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));
          }
          else{
            $status = 204;
            $payload = json_encode(array("mensaje" => "No se encontró el pedido"));

          }

        }
        else
        {
          $status = 404;
          $payload = json_encode(array("mensaje" => "No se colocó el id de pedido"));
        }
        
        $response->getBody()->write($payload);
        return $response
          ->withStatus($status)
          ->withHeader('Content-Type', 'application/json');
    }
}

?>