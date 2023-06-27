<?php

include_once "./Middlewares/AutentificadorJWT.php";
include_once "./Models/Usuario.php";

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class TipoUsuarioMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public static function Admin(Request $request, RequestHandler $handler): Response
    {
        $token = $request->getHeaderLine('Authorization');
        $usuario = AutentificadorJWT::ObtenerData($token);
        $response = new Response();
        if($usuario->_tipo == Usuario::TIPO_ADMIN)
        {
            $response = $handler->handle($request);
        }
        else{
            $response = $response->withStatus(401,"No tiene acceso");
            $response = $response->withHeader('Content-Type', 'application/json');
        }
        return $response
        ->withAddedHeader('Authorization',$token);
    }

    public static function UsuarioRegistrado(Request $request, RequestHandler $handler): Response
    {
        $token = $request->getHeaderLine('Authorization');
        $usuario = AutentificadorJWT::ObtenerData($token);
        $response = new Response();
        $tipoUsuario = $usuario->_tipo;
        if($tipoUsuario == Usuario::TIPO_ADMIN || $tipoUsuario == Usuario::TIPO_CLIENTE)
        {
            $response = $handler->handle($request);
        }
        else{
            $response = $response->withStatus(401,"No tiene acceso");
            $response = $response->withHeader('Content-Type', 'application/json');
        }
        return $response
        ->withAddedHeader('Authorization',$token);
    }
}

?>