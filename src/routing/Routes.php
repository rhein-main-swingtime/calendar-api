<?php
declare(strict_types=1);

/**
 * DOCBLOCKSTUFF
 * @author: mfk
 */

namespace rmswing\routing;

use rmswing\AbstractController;
use rmswing\EventsController;
use rmswing\ParametersInterface;
use Respect\Validation\Validator as v;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use DavidePastore\Slim\Validation\Validation as SlimValidation;
use Slim\App;

class Routes implements ParametersInterface
{

    public const ROUTE_EVENTS = '/events/';

    public static function getDefaultQueryParameters(string $route): array
    {
        $defaults = [
            self::ROUTE_EVENTS => [
                self::START_DATE_PARAMETER => time(),
                self::END_DATE_PARAMETER    => strtotime('+6 months'),
                self::PAGING_PARAMETER      => true,
                self::PAGE_SIZE_PARAMETER   => 5,
                self::SORT_ORDER_PARAMETER  => self::SORT_ORDER_ASC
            ]
        ];


        if (!array_key_exists($route, $defaults)) {
            return [];
        }

        return $defaults[$route];
    }

    public static function getDefaultRouteArgs(string $route): array
    {
        $defaults = [
            self::ROUTE_EVENTS => [
                self::CATEGORY_PARAMETER => self::CATEGORY_DEFAULT,
            ]
        ];

        if (!array_key_exists($route, $defaults)) {
            return [];
        }

        return $defaults[$route];
    }

    public static function getRouteValidations(string $route): array
    {
        $validations = [
            Routes::ROUTE_EVENTS => [

            ]
        ];

        if (array_key_exists($route, $validations) === false) {
            return [];
        }

        return $validations[$route];
    }

    private static function returnRoutes() {
        return [
            self::ROUTE_EVENTS => [
                'controller'    => EventsController::class,
                'method'        => 'get',
                'args'          => '[{category}]'
            ]
        ];
    }



    public static function addRoutes(App $app)
    {
        // It's Magic!
        foreach (self::returnRoutes() as $route => $routeSettings) {
            $controller = $routeSettings['controller'];
            $method = $routeSettings['method'];
            $args = $routeSettings['args'];

            $app->{$method}(
                $route . $args,
                function (Request $request, Response $response, array $args) use ($controller, $route, $app) {

                    // For now this is a purely json based api
                    $response = $response->withHeader('Content-Type', 'application/json');

                    // Bail out from calling the controller if we have input errors
                    if ($request->getAttribute('has_errors') === true) {
                        return Routes::handleErrors($request, $response);
                    }

                    // No errors found, let's inject defaults into the request
                    $queries = array_merge(
                        self::getDefaultQueryParameters($route),
                        $request->getQueryParams()
                    );
                    $request = $request->withQueryParams($queries);

                    $args = array_merge(
                        self::getDefaultRouteArgs($route),
                        $args
                    );

                    /** @var AbstractController $controller */
                    return $controller::create()->execute(
                        $app->logger,
                        $request,
                        $response,
                        $args,
                        $this->get('settings')[$route] ?? null
                    );
                }
            )->add(new SlimValidation(
                self::getRouteValidations($route)
            ));
        }

        return $app;
    }

}
