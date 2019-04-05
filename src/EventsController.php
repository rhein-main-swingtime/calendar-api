<?php
declare (strict_types = 1);



/**
 * DOCBLOCKSTUFF
 * @author mafeka <felix@kakrow.me>
 */

namespace rmswing;

use \Psr\Http\Message\ServerRequestInterface as RequestInterface;
use \Psr\Http\Message\ResponseInterface;
use rmswing\eventsources\Google;
use Composer\XdebugHandler\XdebugHandler;

class EventsController extends AbstractController implements EventParametersInterface
{

    /**
     *
     */
    public static function create(): EventsController
    {
        return new self;
    }

    private static function getDefaultParameters(): array
    {
        return [
            self::START_DATE_PARAMETER  => time(),
            self::END_DATE_PARAMETER    => strtotime('+6 months'),
            // self::PAGING_PARAMETER      => true,
            // self::PAGE_SIZE_PARAMETER   => 5,
            self::SORT_ORDER_PARAMETER  => self::SORT_ORDER_ASC,
            self::OFFSET_PARAMETER      => 0,
            self::LIMIT_PARAMETER       => null,
        ];
    }

    public function execute(
        $logger,
        RequestInterface $request,
        ResponseInterface $response,
        array $args,
        array $settings
    ): ResponseInterface {

        if ($settings === null) {
            throw new \InvalidArgumentException('No Settings, no luck');
        }

        $parameters = array_merge(
            self::getDefaultParameters(),
            $request->getQueryParams()
        );

        // @todo hacky bullshit solution, extend validation class?
        // $parameters['paging'] = filter_var($parameters['paging'], FILTER_VALIDATE_BOOLEAN);
        $parameters['offset'] = (int) $parameters['offset'];
        $parameters['limit'] = (isset($parameters['limit']) ? (int) $parameters['limit'] : null);

        // if nothing provided, let's use the default
        // because - after all - that's what defaults are for!
        $category = $args[self::CATEGORY_PARAMETER] ?? self::CATEGORY_DEFAULT;

        $sourcesToCheck = $this->getRelevantSources($category, $settings);
        $eventCollection = EventCollection::create();
        $labelPrinter = new EventLabeler($settings); // @todo Make this a Service?

        foreach ($sourcesToCheck as $handler => $sources) {
            /** @var Google $handler */
            $handler::create()->fetchEvents(
                $sources,
                $parameters,
                $settings,
                $eventCollection,
                $labelPrinter,
                $logger
            );
        }

        $response->getBody()->write(
            json_encode(
                $eventCollection->getEvents(
                    $parameters['order'],
                    $parameters['start'],
                    $parameters['end'],
                    $args['list'],
                    // $parameters['paging'],
                    // $parameters['pageSize'],
                    $parameters['limit'],
                    $parameters['offset']
                ),
                JSON_PRETTY_PRINT
            )
        );

        return $response;
    }

    private function getRelevantSources(
        ? string $category,
        array $settings
    ): array {

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
