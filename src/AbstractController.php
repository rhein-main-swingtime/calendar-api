<?php
declare(strict_types=1);

/**
 * DOCBLOCKSTUFF
 * @author: mfk
 */

namespace rmswing;

use Monolog\Logger;
use \Psr\Http\Message\ServerRequestInterface as RequestInterface;
use \Psr\Http\Message\ResponseInterface as ResponseInterface;
use Slim\App;

abstract class AbstractController implements ParametersInterface
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
     * @param Logger
     * @param RequestInterface $request
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
