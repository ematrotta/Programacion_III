<?php

use GuzzleHttp\Psr7\Response;
use Psr7Middlewares\Middleware\Expires;

require_once './Models/moneda.php';
require_once './Interfaces/IApiUsable.php';
require_once './Middlewares/AutentificadorJWT.php';
require_once './Models/registro.php';

class MonedaController extends Moneda implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $nacionalidad = $parametros['nacionalidad'];
        $simbolo = $parametros['simbolo'];
        $precio = $parametros['precio'];
        $status = 200;
        if(isset($nombre) && $nombre != "" && isset($nacionalidad) && $nacionalidad != "" && isset($simbolo) && $simbolo != "" && isset($precio) && $precio != "")
        {
          $nombre = strtolower($nombre);
          $nacionalidad = strtolower($nacionalidad);
          $simbolo = strtolower($simbolo);
          $precio = floatval($precio);

          if($precio>0)
          {
            // Creamos el usuario
            $moneda = new Moneda();
            $moneda->_nombre = $nombre;
            $moneda->_simbolo = $simbolo;
            $moneda->_nacionalidad = $nacionalidad;
            $moneda->_precio = $precio;
            if(!Moneda::obtenerMonedaBySimbol($simbolo))
            {
              $moneda->crearMoneda();

              $payload = json_encode(array("mensaje" => "Moneda creada con exito","simbolo"=>$simbolo));
            }
            else{
              $payload = json_encode(array("mensaje" => "El simbolo de la moneda ya esta en uso"));
            }

          }
          else{
            $payload = json_encode(array("mensaje" => "Debe ingresar un precio mayor a 0"));
            $status = 406;
          }

        }
        else{
          $payload = json_encode(array("mensaje" => "Deben completarse todos los datos"));
          $status = 406;

        }
        $response->getBody()->write($payload);
        return $response
          ->withStatus($status)
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        // $queryParams = $request->getQueryParams();
        // $id = $queryParams['idUsuario'];
        $simbolo = $args["simbolo"];
        $moneda = Moneda::obtenerMonedaBySimbol($simbolo);
        if($moneda)
        {
          $payload = json_encode($moneda);

        }
        else{
          $payload = json_encode(array("mensaje" => "No se encontró una moneda con ese simbolo"));

        }
        

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Moneda::obtenerTodos();
        
        if($lista)
        {
          $payload = json_encode(array("listaMonedas" => $lista));
        }
        else{
          $payload = json_encode(array("mensaje" => "No se encontraron monedas registradas"));

        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorNacionalidad($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        $nacionalidad = $parametros['nacionalidad'];
        if(isset($nacionalidad) && !empty($nacionalidad))
        {
          $lista = Moneda::obtenerPorNacionalidad($nacionalidad);
        
          if($lista)
          {
            $payload = json_encode(array("listaMonedas" => $lista));
          }
          else
          {
            $payload = json_encode(array("mensaje" => "No se encuentran monedas con esa nacionalidad"));
          }

        }
        else{
          $payload = json_encode(array("mensaje" => "Debe ingresar los datos correspondientes"));

        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $simboloArg = $args['simbolo'];
        $nombre = $parametros['nombre'];
        $nacionalidad = $parametros['nacionalidad'];
        $precio = $parametros['precio'];

        
        if(isset($simboloArg) && !empty($simboloArg) && isset($nombre) && isset($nacionalidad) && isset($precio))
        {
          $datosModificados = array();
          $nombre = ucwords(strtolower($nombre)," ");
          $simboloArg = strtolower($simboloArg);
          $nacionalidad = strtolower($nacionalidad);
          $precio = floatval($precio);
          $moneda = Moneda::obtenerMonedaBySimbol($simboloArg);

          if($moneda)
          {
            if(!empty($nombre))
            {
             
              $moneda->_nombre = $nombre;
              array_push($datosModificados,"Nombre");
  
            }
            if(!empty($nacionalidad))
            {
              $moneda->_nacionalidad = $nacionalidad;
              array_push($datosModificados,"Nacionalidad");
            }
            if(!empty($precio))
            {
              $moneda->_precio = $precio;
              array_push($datosModificados,"Precio");

            }
            if(count($datosModificados)>0 && Moneda::modificarMoneda($moneda))
            {
              $payload = json_encode(array("mensaje" => "Moneda modificada con exito\nDatos Modificados: ".implode(", ",$datosModificados)."."));
            }
            else
            {
              $payload = json_encode(array("mensaje" => "No se pudo modificar la moneda"));
            }
            

          }
          else{
            $payload = json_encode(array("mensaje" => "Moneda no encontrada"));
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
      $simbolo = $args['simbolo'];
      if(isset($simbolo) && !empty($simbolo))
      {
        $moneda = Moneda::obtenerMonedaBySimbol($simbolo);
        if($moneda)
        {
          if(Moneda::borrarMoneda($moneda))
          {
            $payload = json_encode(array("mensaje" => "Moneda borrada con exito","simbolo"=>$simbolo,"accion"=>Registro::ACCION_BORRAR_CRIPTO,"fechaAccion"=>(new DateTime("now",new DateTimeZone("America/Argentina/Buenos_Aires")))));
          }
          else{
            $payload = json_encode(array("mensaje" => "No se pudo eliminar la moneda"));
          }
          
        }
        else{
          $payload = json_encode(array("mensaje" => "Moneda no encontrado"));

        }

      }
      else{
        $payload = json_encode(array("mensaje" => "Debe ingresar los datos correspoendientes"));
      }
      
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function DescargarTodos($request, $response, $args)
    {
      $listaMonedas = Moneda::obtenerTodos();
      $dataMonedas = array();
      $delimitadorCSV = ";";
      array_push($dataMonedas,"Id Moneda".$delimitadorCSV."Nombre".$delimitadorCSV."Simbolo".$delimitadorCSV."Nacionalidad".$delimitadorCSV."Precio".$delimitadorCSV."Fecha creacion".$delimitadorCSV."Fecha baja");
      foreach($listaMonedas as $moneda)
      {
        $fechaFinalizacion = $moneda->_fechaBaja;
        if($fechaFinalizacion != null)
        {
          $strFechaFinalizacion = date_format($moneda->_fechaBaja,'Y-m-d H:i:s');
        }
        else{
          $strFechaFinalizacion = "";
        }
        array_push($dataMonedas,$moneda->_idMoneda.$delimitadorCSV.$moneda->_nombre.$delimitadorCSV.$moneda->_simbolo.$delimitadorCSV.$moneda->_nacionalidad.$delimitadorCSV.$moneda->_precio.$delimitadorCSV.date_format($moneda->_fechaAlta,'Y-m-d H:i:s').$delimitadorCSV.$strFechaFinalizacion);
      }

      $dataCsv = implode("\n",$dataMonedas);
      
      // Establece los encabezados de respuesta para indicar que se devuelve un archivo CSV
      $response = $response->withHeader('Content-Type','text/csv');
      $response = $response->withHeader('Content-Disposition', 'attachment; filename="monedas.csv"');
      $response->getBody()->write($dataCsv);
      
      // $response->getBody()->write(json_encode($dataUsuarios));
     
      return $response;
    }


}
