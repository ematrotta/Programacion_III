<?php
require_once './Models/Producto.php';
require_once './Interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $titulo = $parametros['titulo'];
        $tiempoPreparacion = $parametros['tiempoPreparacion'];
        $precio = $parametros['precio'];
        $estado = $parametros['estado'];
        $sector = $parametros['sector'];

        // Creamos el producto
        $producto = new Producto();
        $producto->_titulo = $titulo;
        $producto->_tiempoPreparacion = (int)$tiempoPreparacion;
        $producto->_precio = (float)$precio;
        $producto->_estado = $estado;
        $producto->_sector = $sector;

        $producto->crearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos producto por nombre
        // $queryParams = $request->getQueryParams();
        // $id = $queryParams['idProducto'];
        $id = $args["producto"];
        $producto = Producto::obtenerProducto($id);
        
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $args['producto'];
        $titulo = $parametros['titulo'];
        $tiempoPreparacion = (int)$parametros['tiempoPreparacion'];
        $precio = (float)$parametros['precio'];
        $estado = $parametros['estado'];
        $sector = $parametros['sector'];

        $producto = Producto::obtenerProducto($id);
        
        $producto->_titulo = $titulo;
        $producto->_tiempoPreparacion = $tiempoPreparacion;
        $producto->_precio = $precio;
        $producto->_sector = $sector;
        $producto->_estado = $estado;

        Producto::modificarProducto($producto);

        $payload = json_encode(array("mensaje" => "Producto modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        // $parametros = $request->getParsedBody();
        $id = $args['producto'];
        $producto = Producto::obtenerProducto($id);
        Producto::borrarProducto($producto);

        $payload = json_encode(array("mensaje" => "Producto borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}

?>