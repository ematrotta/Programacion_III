<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class CSVMiddleware
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
        $archivos = $request->getUploadedFiles();
        $archivo = $archivos["archivo"];
        $allowedExtensions = ['csv'];
        // $maxSize = 2 * 1024 * 1024;
        $status = 200;
        $response = new Response();
        
        if (isset($archivo) && !empty($archivo) && $archivo->getError() === UPLOAD_ERR_OK) {
            $filename = $archivo->getClientFilename();
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
    
            if (in_array($extension, $allowedExtensions)) {

                $response = $handler->handle($request);
                $payload = json_encode(array("mensaje"=>"Archivo subido correctamente"));
            } else {

                $payload = json_encode(array("mensaje"=>"Archivo no valido"));
            }
        }
        else{
            $payload = json_encode(array("mensaje"=>"Error al descargar archivo"));
            $response = $response->withStatus(401,"No autorizado");
            

        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>