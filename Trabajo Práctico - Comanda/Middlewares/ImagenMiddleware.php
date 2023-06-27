<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ImagenMiddleware
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
    {   $archivos = $request->getUploadedFiles();
        $imagen = $archivos["fotoMesa"];
        $allowedExtensions = ['jpg', 'png', 'gif'];
        $maxSize = 2 * 1024 * 1024;
        $status = 200;
        $response = new Response();
        
        if ($imagen->getError() === UPLOAD_ERR_OK) {
            if (isset($imagen) && !empty($imagen)) {
                $filename = $imagen->getClientFilename();
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
                if (in_array($extension, $allowedExtensions) && $imagen->getSize() <= $maxSize) {

                    // Colocar el validador de carpeta
                    $response = $handler->handle($request);

                    $body = $response->getBody();
                    // Rewind hace que el puntero de lectura del body arranque por el inicio. Sino viene por defecto al final y no permite la lectura
                    $body->rewind();

                    $data = json_decode($body->getContents());
                    $statusResponse = $response->getStatusCode();
                    
                    if($statusResponse == 200)
                    {
                        // Mover el archivo a una carpeta determinada
                        $destination = './Images/ImagenesMesas/' . $data->codigo.'.'.$extension;
                        $imagen->moveTo($destination);
                        $payload = json_encode(array("mensaje"=>"Archivo subido correctamente"));

                    }
                    else{

                        $payload = json_encode(array("mensaje"=>"No se pudo subir el archivo correctamente"));

                    }
                } else {
                    $payload = json_encode(array("mensaje"=>"Archivo no valido"));
                }
            } else {
                $payload = json_encode(array("mensaje"=>"Debe ingresar una imagen"));
                
            }
        
            
        }
        else{
            $payload = json_encode(array("mensaje"=>"Error al descargar archivo"));
            $response = $response->withStatus(401,"No autorizado");
            $response->getBody()->write($payload);

        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>