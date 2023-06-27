<?php

use GuzzleHttp\Psr7\Response;
use Psr7Middlewares\Middleware\Expires;

require_once './Models/usuario.php';
require_once './Models/venta.php';
require_once './Interfaces/IApiUsable.php';
require_once './Middlewares/AutentificadorJWT.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mail = $parametros['mail'];
        $password = $parametros['password'];
        $tipo = $parametros['tipo'];
        $nombre = $parametros['nombre'];
        $status = 200;
        if(isset($nombre) && $nombre != "" && isset($tipo) && $tipo != "" && isset($password) && $password != "" && isset($mail) && $mail != "" && filter_var($mail,FILTER_VALIDATE_EMAIL))
        {
          $tipo = strtolower($tipo);
          $mail = strtolower($mail);
          $nombre = ucwords(strtolower($nombre)," ");
          if($tipo == Usuario::TIPO_ADMIN || $tipo == Usuario::TIPO_CLIENTE)
          {
            // Creamos el usuario
            $usr = new Usuario();
            $usr->_nombre = $nombre;
            $usr->_mail = $mail;
            $usr->_tipo = $tipo;
            $usr->_password = $password;
            if(!Usuario::validarUsuarioExistente($mail))
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
        $mail = $parametros['mail'];
        $password = $parametros['password'];
        $response = new Response();
        if(isset($mail) && !empty($mail) && filter_var($mail,FILTER_VALIDATE_EMAIL) && isset($password) && !empty($password))
        {
          $usuario = Usuario::validarUsuario($mail,$password);
          if($usuario)
          {
              $return = AutentificadorJWT::CrearToken($usuario);
              $response = $response->withHeader('Authorization',$return);
              $usuario->insertarRegistroIngreso();
              $payload = json_encode(array("mensaje"=>"Bienvenido ".$usuario->_nombre,"tipo"=>$usuario->_tipo,"respuesta"=>"ok"));
              
          }
          else{
            $payload = json_encode(array("mensaje"=>"El usuario o la contraseña es incorrecto"));
            $response = $response->withHeader('Content-Type', 'application/json');
          }

        }
        else{
          $payload = json_encode(array("mensaje"=>"Debe ingresar datos"));
          $response = $response->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write($payload);
        return $response;
    }



    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        // $queryParams = $request->getQueryParams();
        // $id = $queryParams['idUsuario'];
        $id = $args["usuario"];
        $usuario = Usuario::obtenerUsuarioById($id);
        if($usuario)
        {
          $payload = json_encode($usuario);

        }
        else{
          $payload = json_encode(array("mensaje" => "No se encontró un usuario con ese ID"));

        }
        

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        
        if($lista)
        {
          $payload = json_encode(array("listaUsuario" => $lista));
        }
        else{
          $payload = json_encode(array("mensaje" => "No se encontraron usuarios registrados"));

        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorMonedaComprada($request, $response, $args)
    {
        $params = $request->getQueryParams();
        $simbolo = $params["simbolo"];

        if(isset($simbolo) && !empty($simbolo))
        {
          $moneda = Moneda::obtenerMonedaBySimbol($simbolo);
          if($moneda)
          {
            $lista = Venta::obtenerTodosTipoMoneda($moneda);
            $arrayUsuarios = array();
            foreach($lista as $venta)
            {
              array_push($arrayUsuarios,$venta->_usuario);
            }
            $arrayUsuarios = array_unique($arrayUsuarios,SORT_REGULAR);
            
            if($lista)
            {
              $payload = json_encode(array("listaUsuario" => $arrayUsuarios));
            }
            else{
              $payload = json_encode(array("mensaje" => "No se encontraron usuarios registrados"));
    
            }

          }
          else
          {
            $payload = json_encode(array("mensaje" => "No se encontraron monedas"));
          }

        }
        else{
          $payload = json_encode(array("mensaje" => "Debe colocar el simbolo de moneda"));
        }


        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $args['usuario'];
        $nombre = $parametros['nombre'];
        $mail = $parametros['mail'];
        $tipo = $parametros['tipo'];
        $password = $parametros['password'];

        
        if(isset($id) && !empty($id) && isset($nombre) && isset($mail) && isset($tipo) && isset($password))
        {
          $datosModificados = array();
          $usuario = Usuario::obtenerUsuarioById($id);
          $nombre = ucwords(strtolower($nombre)," ");
          $tipo = strtolower($tipo);
          if($usuario)
          {
            if(!empty($nombre))
            {
             
              $usuario->_nombre = $nombre;
              array_push($datosModificados,"Nombre");
  
            }
            if(!empty($tipo) && ($tipo == Usuario::TIPO_ADMIN || $tipo == Usuario::TIPO_CLIENTE))
            {
              $usuario->_tipo = $tipo;
              array_push($datosModificados,"Tipo");

            }
            if(!empty($mail) && filter_var($mail,FILTER_VALIDATE_EMAIL))
            {
              $usuario->_mail = $mail;
              array_push($datosModificados,"Mail");
            }
            if(!empty($password))
            {
              $usuario->_password = $password;
              array_push($datosModificados,"Password");

            }
            if(count($datosModificados)>0 && Usuario::modificarUsuario($usuario))
            {
              $payload = json_encode(array("mensaje" => "Usuario modificado con exito\nDatos Modificados: ".implode(", ",$datosModificados)."."));
            }
            else
            {
              $payload = json_encode(array("mensaje" => "No se pudo modificar el usuario"));
            }
            

          }
          else{
            $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
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
      $id = $args['usuario'];
      if(isset($id) && !empty($id))
      {
        $usuario = Usuario::obtenerUsuarioById($id);
        if($usuario)
        {
          if(Usuario::borrarUsuario($usuario))
          {
            $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
          }
          else{
            $payload = json_encode(array("mensaje" => "No se pudo eliminar el usuario"));
          }
          
        }
        else{
          $payload = json_encode(array("mensaje" => "Usuario no encontrado"));

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
