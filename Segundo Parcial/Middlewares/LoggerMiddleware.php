<?php

use Firebase\JWT\ExpiredException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class LoggerMiddleware
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
        $return = false;
        $token = $request->getHeaderLine('Authorization');
        $response = new Response();
        if (!empty($token)) {
            try{

                AutentificadorJWT::VerificarToken($token);
                $response = $handler->handle($request);

            }
            catch(ExpiredException)
            {
                $payload = json_encode(array("Mensaje"=>"La sesion a expirado. Debe iniciar sesión"));
                $response = $response->withStatus(401,"No autorizado");
                $response = $response->withHeader('Content-Type', 'application/json');
                $response->getBody()->write($payload);

            }
            catch(Exception $e)
            {
                $payload = json_encode(array("Mensaje"=>"Error Inesperado ".$e));
                $response = $response->withHeader('Content-Type', 'application/json');
                $response->getBody()->write($payload);
            }
            
        }
        else{
            $payload = json_encode(array("Mensaje"=>"Debe iniciar sesión"));
            $response = $response->withStatus(401,"No autorizado");
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write($payload);

        }
        return $response;
    }
}

?>