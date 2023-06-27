<?php

use GuzzleHttp\Psr7\Response;
use Psr7Middlewares\Middleware\Expires;

require_once './Models/venta.php';
require_once './Interfaces/IApiUsable.php';
require_once './Middlewares/AutentificadorJWT.php';
include_once './Models/usuario.php';
include_once './Models/moneda.php';

class VentaController extends Venta implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $token = $request->getHeaderLine('Authorization');
      $parametros = $request->getParsedBody();

      $usuario = AutentificadorJWT::ObtenerData($token);
      $idUsuario = $usuario->_idUsuario;
      $simbolCripto = $parametros['simbolCripto'];
      $cantidad = $parametros['cantidad'];

      if(isset($idUsuario) && !empty($idUsuario) && isset($simbolCripto) && !empty($simbolCripto) && isset($cantidad) && !empty($cantidad) )
      {
        $simbolCripto = strtolower($simbolCripto);
        $cantidad = intval($cantidad);
        $moneda = Moneda::obtenerMonedaBySimbol($simbolCripto);
        $usuario = Usuario::obtenerUsuarioById($idUsuario);
        if($moneda && $usuario && $cantidad>0)
        {
          // Creamos el usuario
          $venta = new Venta();
          $venta->_moneda = $moneda;
          $venta->_usuario = $usuario;
          $venta->_cantidad = $cantidad;
          $idVenta = $venta->crearVenta();
          $venta = Venta::obtenerVentaById($idVenta);
          if($venta)
          {
            $payload = json_encode(array("mensaje" => "Venta creada con exito","simbolo"=>$moneda->_simbolo,"nombreCliente"=>$usuario->_nombre,"fechaVenta"=>$venta->_fechaVenta));
          }
          else{
            $payload = json_encode(array("mensaje" => "No se pudo realizar la venta"));
          }

        }
        else{
          $payload = json_encode(array("mensaje" => "No se pudo encontrar la moneda, el usuario o la cantidad es igual a 0"));
        }

      }
      else{
        $payload = json_encode(array("mensaje" => "Deben completarse todos los datos"));

      }
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        // $queryParams = $request->getQueryParams();
        // $id = $queryParams['idUsuario'];
        $idVenta = $args["idVenta"];
        $venta = Venta::obtenerVentaById($idVenta);
        if($venta)
        {
          $payload = json_encode($venta);

        }
        else{
          $payload = json_encode(array("mensaje" => "No se encontró una venta con ese id"));

        }
        

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Venta::obtenerTodos();
        
        if($lista)
        {
          $payload = json_encode(array("listaVentas" => $lista));
        }
        else{
          $payload = json_encode(array("mensaje" => "No se encontraron ventas registradas"));

        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorNacionalidad($request, $response, $args)
    {
        $params = $request->getQueryParams();
        $nacionalidad = $params["nacionalidad"];
        $strFechaDesde = $params["desde"];
        $strFechaHasta = $params["hasta"];

        if(isset($nacionalidad) && !empty($nacionalidad) && isset($strFechaDesde) && isset($strFechaHasta))
        {
          $nacionalidad = strtolower($nacionalidad);
          if(!empty($strFechaHasta))
          {
            $fechaHasta = DateTime::createFromFormat("d/m/Y",$strFechaHasta);
          }
          else{
            $fechaHasta = (new DateTime("tomorrow",new DateTimeZone("America/Argentina/Buenos_Aires")));
          }
          if(!empty($strFechaDesde))
          {
            $fechaDesde = DateTime::createFromFormat("d/m/Y",$strFechaDesde);
          }
          else{
            $fechaDesde = (new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires")));
          }

          if(!$fechaDesde || !$fechaHasta)
          {
            $payload = json_encode(array("mensaje" => "Debe ingresar el formato correcto de fecha: d/m/Y"));
          }
          else{
            $fechaDesde = $fechaDesde->setTime(0,0,0);
            $fechaHasta = $fechaHasta->setTime(0,0,0);
            if($fechaDesde<=$fechaHasta)
            {
              $monedas = Moneda::obtenerPorNacionalidad($nacionalidad);
              if(count($monedas)>0)
              {
                $arrayVentas = array();
                foreach($monedas as $moneda)
                {
                  $lista = Venta::obtenerTodosTipoMoneda($moneda);
                  if(count($lista)>0)
                  {
                    foreach($lista as $venta)
                    {
                      if($venta->_fechaVenta >= $fechaDesde && $venta->_fechaVenta <= $fechaHasta)
                      {
                        array_push($arrayVentas,$venta);
                      }
                    }
                    
                  }

                }
                if(count($arrayVentas)>0)
                {
                  $payload = json_encode(array("listaVentas" => $arrayVentas));
                }
                else{
                  $payload = json_encode(array("mensaje" => "No se encontraron ventas en ese rango de fechas"));
                }
                

              }
              else{
                $payload = json_encode(array("mensaje" => "No se encontraron monedas con esa nacionalidad"));
              }
            }
            else{
              $payload = json_encode(array("mensaje" =>"La fecha desde debe ser menor o igual a la fecha hasta"));
            }

          }

        }
        else{
          $payload = json_encode(array("mensaje" => "Debe colocar una nacionalidad"));
        }


        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idVenta = $args['idVenta'];
        $simbolMoneda = $parametros['simbolCripto'];
        $cantidad = $parametros['cantidad'];
        
        if(isset($idVenta) && !empty($idVenta) && isset($simbolMoneda) && isset($cantidad))
        {
          $datosModificados = array();
          $simbolMoneda = strtolower($simbolMoneda);
          $cantidad = intval($cantidad);
          $venta = Venta::obtenerVentaById($idVenta);
          $moneda = Moneda::obtenerMonedaBySimbol($simbolMoneda);

          if($venta && $moneda)
          {
            if(!empty($moneda))
            {
             
              $venta->_moneda = $moneda;
              array_push($datosModificados,"Moneda, Precio, Total");
  
            }
            if(!empty($cantidad))
            {
              $venta->_cantidad = $cantidad;
              array_push($datosModificados,"Cantidad");
            }
            if(count($datosModificados)>0 && Venta::modificarVenta($venta))
            {
              $payload = json_encode(array("mensaje" => "Venta modificada con exito\nDatos Modificados: ".implode(", ",$datosModificados)."."));
            }
            else
            {
              $payload = json_encode(array("mensaje" => "No se pudo modificar la venta"));
            }
            

          }
          else{
            $payload = json_encode(array("mensaje" => "Venta no encontrada"));
          }
        }
        else{
          $payload = json_encode(array("mensaje" => "Debe ingresar los datos correspoendientes"));

        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
      // $parametros = $request->getParsedBody();
      $idVenta = $args['idVenta'];
      if(isset($idVenta) && !empty($idVenta))
      {
        $venta = Venta::obtenerVentaById($idVenta);
        if($venta)
        {
          if(Venta::borrarVenta($venta))
          {
            $payload = json_encode(array("mensaje" => "Venta borrada con exito"));
          }
          else{
            $payload = json_encode(array("mensaje" => "No se pudo eliminar la venta"));
          }
          
        }
        else{
          $payload = json_encode(array("mensaje" => "Venta no encontrado"));

        }

      }
      else{
        $payload = json_encode(array("mensaje" => "Debe ingresar los datos correspoendientes"));
      }
      
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }


}
