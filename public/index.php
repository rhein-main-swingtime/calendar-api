<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__
    . DIRECTORY_SEPARATOR
    . '..'
    . DIRECTORY_SEPARATOR
    . 'vendor'
    . DIRECTORY_SEPARATOR
    . 'autoload.php';

require_once __DIR__
    . DIRECTORY_SEPARATOR
    . '..'
    . DIRECTORY_SEPARATOR
    . 'config/config.php';


$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

// @todo move routes into separate file
$app->any('/events/[{category}]', function (Request $request, Response $response, array $args) {

    return rmswing\EventsHandler::create()->execute(
        $request,
        $response,
        $args,
        $this->get('settings')['events'] ?? null
    );

});

try {
    $app->run();
} catch (\Exception $e) {
    var_dump($e->getMessage());
}
