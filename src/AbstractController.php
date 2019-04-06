<?php
declare(strict_types=1);

namespace rmswing;

use Monolog\Logger;
use \Psr\Http\Message\ServerRequestInterface as RequestInterface;
use \Psr\Http\Message\ResponseInterface as ResponseInterface;
use Slim\App;

/**
 * Abstract Controller should be implemented by all controllers
 *
 * @author mafeka https://github.com/mafeka
 */
abstract class AbstractController
{

    /**
     * This should always return a new instance of the class.
     *
     * Let's not muck about with statefull stuff.
     *
     * @return AbstractController
     */
    abstract public static function create();

    /**
     * @param Logger $logger
     * @param RequestInterface $request Request
     * @param ResponseInterface $response
     * @param array $args
     * @param array $settings
     * @return ResponseInterface
     */
    abstract public function execute(
        Logger $logger,
        RequestInterface $request,
        ResponseInterface $response,
        array $args,
        array $settings
    ): ResponseInterface;
}
