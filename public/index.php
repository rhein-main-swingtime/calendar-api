<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Container\ContainerInterface;
use Respect\Validation\Validator as v;

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

$container['logger'] = function ($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler(__DIR__ . '../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['eventsController'] = function (ContainerInterface $c) {
    /** @var Monolog\Logger $logger */
    $logger = $c->get('logger');
    $logger->addDebug('Created eventsController');
    return \rmswing\EventsController::create();
};


$app->get(
    '/events/' . \rmswing\EventsController::CATEGORY_PARAMETER_URL,
    function (Request $request, Response $response, $args) use ($app) {

        // For now this is a purely json based api
        $response = $response->withHeader('Content-Type', 'application/json');

        // Bail out from calling the controller if we have input errors
        if ($request->getAttribute('has_errors') === true) {
            $response->getBody()->write(
                json_encode(
                    $request->getAttribute('errors'),
                    JSON_PRETTY_PRINT
                )
            );
            $response = $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);

            return $response;
        }

        $container = $app->getContainer();
        $ControllerSettings = $container->get('settings')['EventsRoute'];
        $Logger = $container->get('logger');

        return \rmswing\EventsController::create()->execute(
            $Logger,
            $request,
            $response,
            $args,
            $ControllerSettings
        );
    }
)->add(new \DavidePastore\Slim\Validation\Validation(
    // phpcs:disable
    [
        \rmswing\EventsController::CATEGORY_PARAMETER   => v::optional(v::in(\rmswing\EventsController::CATEGORY_VALID)),
        \rmswing\EventsController::END_DATE_PARAMETER   => v::optional(v::numeric()->positive()),
        \rmswing\EventsController::PAGE_SIZE_PARAMETER  => v::optional(v::numeric()->positive()),
        \rmswing\EventsController::PAGING_PARAMETER     => v::optional(v::boolVal()),
        \rmswing\EventsController::SORT_ORDER_PARAMETER => v::optional(v::in(\rmswing\EventsController::SORT_ORDER_VALID)),
        \rmswing\EventsController::START_DATE_PARAMETER => v::optional(v::numeric()->positive()),
        \rmswing\EventsController::OFFSET_PARAMETER     => v::optional(v::numeric()->positive()),
        \rmswing\EventsController::LIMIT_PARAMETER      => v::optional(v::numeric()->positive()),
    ]
    // phpcs:enable
));

try {
    $app->run();
} catch (\Exception $e) {
    // @todo on public this should just log it and show a generic error,
    // @todo on DEV this could use whoops
    var_dump($e->getMessage());
}
