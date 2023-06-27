<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/vendor/autoload.php';
include_once './Middlewares/TipoUsuarioMiddleware.php';
include_once './Middlewares/ImagenMiddleware.php';
include_once './Middlewares/LoggerMiddleware.php';
include_once './Middlewares/RegistroAccionMiddleware.php';
include_once './Controllers/UsuarioController.php';
include_once './Controllers/MonedaController.php';
include_once './Controllers/VentaController.php';

// Instantiate App
$app = AppFactory::create();
$app->setBasePath('/Segundo Parcial');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();



// Routes
$app->post('/login',\UsuarioController::class.':Login');

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('/moneda', \UsuarioController::class . ':TraerPorMonedaComprada');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
    $group->put('/{usuario}', \UsuarioController::class . ':ModificarUno');
    $group->delete('/{usuario}', \UsuarioController::class . ':BorrarUno');
  })->add(\TipoUsuarioMiddleware::class.':Admin')->add(new LoggerMiddleware());

  $app->group('/monedas', function (RouteCollectorProxy $group) {
    $group->get('/descargar', \MonedaController::class . ':DescargarTodos')->add(\TipoUsuarioMiddleware::class.':Admin')->add(new LoggerMiddleware());
    $group->get('/nacionalidad', \MonedaController::class . ':TraerPorNacionalidad');
    $group->get('/{simbolo}', \MonedaController::class . ':TraerUno')->add(\TipoUsuarioMiddleware::class.':UsuarioRegistrado')->add(new LoggerMiddleware());
    $group->get('[/]', \MonedaController::class . ':TraerTodos');
    $group->post('[/]', \MonedaController::class . ':CargarUno')->add(new ImagenMiddleware())->add(\TipoUsuarioMiddleware::class.':Admin')->add(new LoggerMiddleware());
    $group->put('/{simbolo}', \MonedaController::class . ':ModificarUno')->add(\TipoUsuarioMiddleware::class.':Admin')->add(new LoggerMiddleware());
    $group->delete('/{simbolo}', \MonedaController::class . ':BorrarUno')->add(new RegistroAccionMiddleware())->add(\TipoUsuarioMiddleware::class.':Admin')->add(new LoggerMiddleware());
  });

  $app->group('/ventas', function (RouteCollectorProxy $group) {
    $group->get('/nacionalidad', \VentaController::class . ':TraerPorNacionalidad')->add(\TipoUsuarioMiddleware::class.':Admin');
    $group->get('/{idVenta}', \VentaController::class . ':TraerUno')->add(\TipoUsuarioMiddleware::class.':Admin');
    $group->get('[/]', \VentaController::class . ':TraerTodos')->add(\TipoUsuarioMiddleware::class.':Admin');
    $group->post('[/]', \VentaController::class . ':CargarUno')->add(new ImagenMiddleware())->add(\TipoUsuarioMiddleware::class.':UsuarioRegistrado');
    $group->put('/{idVenta}', \VentaController::class . ':ModificarUno')->add(\TipoUsuarioMiddleware::class.':Admin');
    $group->delete('/{idVenta}', \VentaController::class . ':BorrarUno')->add(\TipoUsuarioMiddleware::class.':Admin');
  })->add(new LoggerMiddleware());


$app->run();
