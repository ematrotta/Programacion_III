<?php

use Psr7Middlewares\Middleware\Expires;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

include_once "./Models/moneda.php";
include_once "./Models/usuario.php";
include_once "./Models/registro.php";

class RegistroAccionMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    { 
        $token = $request->getHeaderLine('Authorization');
        $usuario = AutentificadorJWT::ObtenerData($token);
        $response = new Response();
        
        if (isset($usuario->_idUsuario)) {
            $response = $handler->handle($request);
            $body = $response->getBody();
            // Rewind hace que el puntero de lectura del body arranque por el inicio. Sino viene por defecto al final y no permite la lectura
            $body->rewind();

            $data = json_decode($body->getContents());
            $statusResponse = $response->getStatusCode();
            if($statusResponse == 200)
            {
                // Caso que corresponda a alta de moneda
                if(property_exists($data,'simbolo') && property_exists($data,'accion') && property_exists($data,'fechaAccion'))
                {
                    $simbolo = $data->simbolo;
                    $fecha = DateTime::createFromFormat("Y-m-d H:i:s.u", $data->fechaAccion->date);
                    $accion = $data->accion;

                    $registro = new Registro();
                    $registro->_usuario = Usuario::obtenerUsuarioById($usuario->_idUsuario);
                    $registro->_moneda = Moneda::obtenerMonedaBySimbol($simbolo);
                    $registro->_accion = $accion;
                    $registro->_fechaAccion = $fecha;
                    $registro->crearRegistro();
                }
            }
        }
        else{
            $payload = json_encode(array("mensaje"=>"Debe haber un usuario logueado"));
            $response->getBody()->write($payload);

        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>