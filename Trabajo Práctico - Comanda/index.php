<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/vendor/autoload.php';
require_once './Controllers/UsuarioControllers.php';
require_once './Controllers/MesaController.php';
require_once './Controllers/ProductoController.php';
require_once './Controllers/PedidoController.php';
require_once './db/AccesoDatos.php';
require_once './Middlewares/LoggerMiddleware.php';
require_once './Middlewares/TipoUsuarioMiddleware.php';
require_once './Middlewares/ImagenMiddleware.php';
require_once './Middlewares/CSVMiddleware.php';

// Instantiate App
$app = AppFactory::create();
$app->setBasePath('/Trabajo Práctico - Comanda');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// peticiones
$app->post('/login',\UsuarioController::class.':Login');


$app->group('/usuarios', function (RouteCollectorProxy $group) {
    // $group->get('/{idUsuario}', \UsuarioController::class . ':TraerUno');
    $group->get('/descargar', \UsuarioController::class . ':DescargarTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
    $group->post('/masiva', \UsuarioController::class . ':CargaMasiva')->add(new CSVMiddleware());
    $group->put('/{usuario}', \UsuarioController::class . ':ModificarUno');
    $group->delete('/{usuario}', \UsuarioController::class . ':BorrarUno');
  })->add(\TipoUsuarioMiddleware::class.':Socio')->add(new LoggerMiddleware());

  $app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->get('/{producto}', \ProductoController::class . ':TraerUno');
    $group->post('[/]', \ProductoController::class . ':CargarUno')->add(\TipoUsuarioMiddleware::class.':Socio');
    $group->put('/{producto}', \ProductoController::class . ':ModificarUno')->add(\TipoUsuarioMiddleware::class.':Socio');
    $group->delete('/{producto}', \ProductoController::class . ':BorrarUno')->add(\TipoUsuarioMiddleware::class.':Socio');
  })->add(new LoggerMiddleware());

  $app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(\TipoUsuarioMiddleware::class.':SocioOMozo')->add(new LoggerMiddleware());
    $group->get('/pendientes', \PedidoController::class . ':TraerPendientes')->add(new LoggerMiddleware());
    $group->get('/{pedido}', \PedidoController::class . ':TraerUno');
    $group->post('[/]', \PedidoController::class . ':CargarUno')->add(\TipoUsuarioMiddleware::class.':SocioOMozo')->add(new LoggerMiddleware());
    $group->put('/estado/{pedido}', \PedidoController::class . ':ModificarEstadoPedido')->add(new LoggerMiddleware());
    $group->put('/{pedido}', \PedidoController::class . ':ModificarUno')->add(new LoggerMiddleware());
    $group->delete('/{pedido}', \PedidoController::class . ':BorrarUno')->add(\TipoUsuarioMiddleware::class.':SocioOMozo')->add(new LoggerMiddleware());
  });

  $app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    $group->get('/{mesa}', \MesaController::class . ':TraerUno');
    $group->post('[/]', \MesaController::class . ':CargarUno')->add(new ImagenMiddleware());
    $group->put('/estado/{mesa}', \MesaController::class . ':ModificarEstadoMesa');
    $group->put('/{mesa}', \MesaController::class . ':ModificarUno');
    $group->delete('/{mesa}', \MesaController::class . ':BorrarUno')->add(\TipoUsuarioMiddleware::class.':Socio');
  })->add(\TipoUsuarioMiddleware::class.':SocioOMozo')->add(new LoggerMiddleware());


$app->run();

?>
