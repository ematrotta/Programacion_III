<?php

use Psr7Middlewares\Middleware\Expires;

require_once './Models/Usuario.php';
require_once './Interfaces/IApiUsable.php';
require_once './Middlewares/AutentificadorJWT.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $user = $parametros['user'];
        $tipo = $parametros['tipo'];
        $password = $parametros['password'];
        $status = 200;
        if(isset($nombre) && $nombre != "" && isset($user) && $user != "" && isset($tipo) && $tipo != "" && isset($password) && $password != "")
        {
          $tipo = strtolower($tipo);
          $nombre = ucwords(strtolower($nombre)," ");
          if($tipo == Usuario::SECTOR_BAR || $tipo == Usuario::SECTOR_CERVEZA || $tipo == Usuario::SECTOR_COCINA || $tipo == Usuario::SECTOR_MESA || $tipo == Usuario::SECTOR_SOCIO)
          {
            // Creamos el usuario
            $usr = new Usuario();
            $usr->_nombre = $nombre;
            $usr->_user = $user;
            $usr->_tipo = $tipo;
            $usr->_password = $password;
            if(!Usuario::validarUsuarioExistente($user))
            {
              $usr->crearUsuario();
              $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
            }
            else{
              $payload = json_encode(array("mensaje" => "El nombre de usuario ya se encuentra en uso"));
            }



          }
          else{
            $payload = json_encode(array("mensaje" => "No se ingresó el tipo de usuario correcto"));
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

    public function Login($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $user = $parametros['user'];
        $password = $parametros['password'];
        $status = 200;
        if(isset($user) && !empty($user) && isset($password) && !empty($password))
        {
          $usuario = Usuario::validarUsuario($user,$password);
          if($usuario)
          {
              $return = AutentificadorJWT::CrearToken($usuario);
              $response = $response->withHeader('Authorization',$return);
              $usuario->insertarRegistroIngreso();
              $payload = json_encode(array("Mensaje"=>"Bienvenido ".$usuario->_nombre));
              
          }
          else{
            $payload = json_encode(array("Mensaje"=>"El usuario o la contraseña es incorrecto"));
            $response = $response->withHeader('Content-Type', 'application/json');
            $status = 401;
          }

        }
        $response->getBody()->write($payload);
        return $response
          ->withStatus($status);
    }



    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        // $queryParams = $request->getQueryParams();
        // $id = $queryParams['idUsuario'];
        $id = $args["usuario"];
        $usuario = Usuario::obtenerUsuario($id);
        $status = 200;
        if($usuario)
        {
          $payload = json_encode($usuario);

        }
        else{
          $payload = json_encode(array("mensaje" => "No se encontró un usuario con ese ID"));
          $status = 204;

        }
        

        $response->getBody()->write($payload);
        return $response
          ->withStatus($status)
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $status = 200;
        
        if($lista)
        {
          $payload = json_encode(array("listaUsuario" => $lista));
        }
        else{
          $payload = json_encode(array("mensaje" => "No se encontraron usuarios registrados"));
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

        $id = $args['usuario'];
        $nombre = $parametros['nombre'];
        $user = $parametros['user'];
        $tipo = $parametros['tipo'];
        $password = $parametros['password'];

        $status = 200;
        if(isset($id) && !empty($id) && isset($nombre) && isset($user) && isset($tipo) && isset($password))
        {
          $usuario = Usuario::obtenerUsuario($id);
          $nombre = ucwords(strtolower($nombre)," ");
          $tipo = strtolower($tipo);
          if($usuario)
          {
            if(!empty($nombre))
            {
             
              $usuario->_nombre = $nombre;
  
            }
            if(!empty($tipo) && ($tipo == Usuario::SECTOR_BAR || $tipo == Usuario::SECTOR_CERVEZA || $tipo == Usuario::SECTOR_COCINA || $tipo == Usuario::SECTOR_MESA || $tipo == Usuario::SECTOR_SOCIO))
            {
              $usuario->_tipo = $tipo;

            }
            if(!empty($user))
            {
              $usuario->_user = $user;
            }
            if(!empty($password))
            {
              $usuario->_password = $password;

            }
            Usuario::modificarUsuario($usuario);
            $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

          }
          else{
            $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
            $status = 204;
          }
        }
        else{
          $payload = json_encode(array("mensaje" => "Debe ingresar los datos correspoendientes"));
          $status = 406;

        }



        $response->getBody()->write($payload);
        return $response
          ->withStatus($status)
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
      // $parametros = $request->getParsedBody();
      $id = $args['usuario'];
      $status = 200;
      if(isset($id) && !empty($id))
      {
        $usuario = Usuario::obtenerUsuario($id);
        if($usuario)
        {
          Usuario::borrarUsuario($usuario);
          $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
        }
        else{
          $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
          $status = 204;

        }

      }
      else{
        $payload = json_encode(array("mensaje" => "Debe ingresar los datos correspoendientes"));
        $status = 406;
      }
      
      $response->getBody()->write($payload);
      return $response
        ->withStatus($status)
        ->withHeader('Content-Type', 'application/json');
    }

    public function CargaMasiva($request, $response, $args)
    {
      $archivos = $request->getUploadedFiles();
      $archivo = $archivos["archivo"];
      $contadorUsuariosCorrectos = 0;
      $contadorUsuariosErroneos = 0;
      $delimitadorCSV = ";";

      $csvData = $archivo->getStream()->getContents();
      $lineas = explode("\n",$csvData);
      // El metodo explode si no encuentra datos, la siguiente linea la determina en blanco por lo que debemos filtrar el listado
      $lineas = array_filter($lineas,function($valor){return trim($valor) !=="";});

      // Verifico que el formato sea el correcto obteniendo encabezados
      if($lineas && count($lineas)>0)
      {
        $headers = $lineas[0];
        $camposHeader = str_getcsv($headers,$delimitadorCSV);
        if($camposHeader[0]=="nombre" && $camposHeader[1]=="user" && $camposHeader[2]=="password" && $camposHeader[3]=="tipo")
        {
          array_splice($lineas,0,1);
          $filasErroneas = array();

          foreach($lineas as $index => $linea)
          {
            try{
              $campos = str_getcsv($linea,$delimitadorCSV);
              $usuario = new Usuario();
              $nombre = $campos[0];
              $user = $campos[1];
              $password = $campos[2];
              $tipo = $campos[3];
      
              if(isset($nombre) && !empty($nombre) && isset($user) && !empty($user) && isset($password) && !empty($password) && isset($tipo) && !empty($tipo) && is_string($nombre) && is_string($user) && is_string($password) && is_string($tipo))
              {
                $nombre = ucwords(strtolower($nombre)," ");
                $tipo = strtolower($tipo);
                if($tipo == Usuario::SECTOR_BAR || $tipo == Usuario::SECTOR_CERVEZA || $tipo == Usuario::SECTOR_COCINA || $tipo == Usuario::SECTOR_MESA || $tipo == Usuario::SECTOR_SOCIO)
                {
                  $usuario = new Usuario();
                  $usuario->_nombre = $nombre;
                  $usuario->_password = $password;
                  $usuario->_user = $user;
                  $usuario->_tipo = $tipo;
                  if(!Usuario::validarUsuarioExistente($user))
                  {
                    $usuario->crearUsuario();
                    $contadorUsuariosCorrectos ++;
                  }
                  else{

                    $motivoFilaError = "Fila ".($index+1).": El user ya se encuentra en uso";
                    array_push($filasErroneas,$motivoFilaError);
                    $contadorUsuariosErroneos++;
                    continue;
                  }
                  
                }
                else{
                  $motivoFilaError = "Fila ".($index+1).": El tipo de usuario es erroneo";
                  array_push($filasErroneas,$motivoFilaError);
                  $contadorUsuariosErroneos++;
                  continue;
                }
              }
              else{
                $motivoFilaError = "Fila ".($index+1).": Alguno de los campos se encuentran vacios";
                array_push($filasErroneas,$motivoFilaError);
                $contadorUsuariosErroneos++;
                continue;
              }


            }
            catch(Exception $e)
            {
              $motivoFilaError = "Fila ".($index+1).": Alguno de los campos no se encuentran cargados ".$e;
              array_push($filasErroneas,$motivoFilaError);
              $contadorUsuariosErroneos++;
              continue;
            }

          }

          $payload = json_encode(array("mensaje" => "Se procesaron los usuarios: Correctos ".$contadorUsuariosCorrectos."; Erroneos ".$contadorUsuariosErroneos."\n".implode("\n",$filasErroneas)));


        }
        else{
          $payload = json_encode(array("mensaje" => "El archivo no tiene el formato correcto. Debe ser: nombre,user,password,tipo"));

        }
      }
      else{
        $payload = json_encode(array("mensaje" => "El archivo no tiene contenido"));

      }
      
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function DescargarTodos($request, $response, $args)
    {
      $listaUsuarios = Usuario::obtenerTodos();
      $dataUsuarios = array();
      $delimitadorCSV = ";";
      array_push($dataUsuarios,"Id Usuario".$delimitadorCSV."Nombre".$delimitadorCSV."User".$delimitadorCSV."Password".$delimitadorCSV."Tipo".$delimitadorCSV."Fecha creacion".$delimitadorCSV."Fecha baja");
      foreach($listaUsuarios as $usuario)
      {
        $fechaFinalizacion = $usuario->_fechaFinalizacion;
        if($fechaFinalizacion != null)
        {
          $strFechaFinalizacion = date_format($usuario->_fechaFinalizacion,'Y-m-d H:i:s');
        }
        else{
          $strFechaFinalizacion = "";
        }
        array_push($dataUsuarios,$usuario->_idUsuario.$delimitadorCSV.$usuario->_nombre.$delimitadorCSV.$usuario->_user.$delimitadorCSV.$usuario->_password.$delimitadorCSV.$usuario->_tipo.$delimitadorCSV.date_format($usuario->_fechaCreacion,'Y-m-d H:i:s').$delimitadorCSV.$strFechaFinalizacion);
      }

      $dataCsv = implode("\n",$dataUsuarios);
      
      // Establece los encabezados de respuesta para indicar que se devuelve un archivo CSV
      $response = $response->withHeader('Content-Type','text/csv');
      $response = $response->withHeader('Content-Disposition', 'attachment; filename="usuarios.csv"');
      $response->getBody()->write($dataCsv);
      
      // $response->getBody()->write(json_encode($dataUsuarios));
     
      return $response;
    }


}

?>