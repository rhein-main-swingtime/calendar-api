<?php
declare(strict_types=1);



/**
 * DOCBLOCKSTUFF
 * @author: mfk
 */

namespace rmswing;

use \Psr\Http\Message\ServerRequestInterface as RequestInterface;
use \Psr\Http\Message\ResponseInterface as ResponseInterface;

class EventsHandler
{

    /** @var null|EventCollection */
    private $collection = null;

    public const FALLBACK_CATEGORY = 'all';

    public static function create(): EventsHandler
    {
        return new self;
    }

    public function execute(
        RequestInterface $request,
        ResponseInterface $response,
        array $args,
        array $settings
    ): ResponseInterface {
        if ($settings === null) {
            throw new \InvalidArgumentException('No Settings, no luck');
        }

        $queryParams = $request->getQueryParams();
        $sourcesToCheck = $this->getRelevantSources($args, $settings);
        $this->collection = new EventCollection();

        foreach ($sourcesToCheck as $handler => $sources) {
            $className = $handler;
            $sourceHandler = new $className($sources, $settings[$className]);
            $this->collection->addCollectionToCollection($sourceHandler->getEvents());
        }

        $response->getBody()->write(json_encode($this->collection, JSON_PRETTY_PRINT));
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    private function getRelevantSources(
        array $args,
        array $settings
    ): array {

        // Let's set the category we're looking for to something useful
        if (!isset($args['category']) || (string) $args['category'] !== '') {
            $category = self::FALLBACK_CATEGORY;
        } else {
            $category = (string) $args['category'];
        }

        $out = [];

        foreach ($settings['sources'] as $source) {
            if (!in_array($category, $source['categories'])) {
                // nothing to do here.
                continue;
            }
            $handler = $source['handler'];
            $out[$handler][] = $source['id'];
        }

        return $out;
    }
    }
