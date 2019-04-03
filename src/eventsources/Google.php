<?php
declare(strict_types=1);


namespace rmswing\eventsources;

use Monolog\Logger;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use rmswing\Event;
use rmswing\EventCollection;
use rmswing\EventLabeler;

/**
 * Implements Google Calendar API as an Eventsource
 *
 * @author mafeka https://github.com/mafeka
 * @package rmswing
 */
class Google
{

    /** @var array  */
    protected $clientParams = [];

    /** @var Google_Client|null */
    protected $client;

    /** @var array */
    protected $sources;

    /** @var array */
    protected $labelMapping;

    /** @var string */
    protected $authConfig;

    /** @var EventLabeler */
    protected $eventLabeler;

    /** @var EventCollection */
    protected $collection;

    /**
     * @return Google
     */
    public static function create(): Google
    {
        return new self;
    }

    /**
     * @param array $sourceCategories
     * @param array $labels
     * @return array
     *
     * @todo move this to an abstract class
     * @todo abstract this some more
     */
    protected function getEventCategories(array $sourceCategories, array $labels): array
    {
        $out = [];
        foreach ($sourceCategories as $category) {
            if (array_key_exists($category, $labels)) {
                $out[] = $labels[$category];
            }
        }
        return $out;
    }

    /**
     * @param array $params
     * @return array
     */
    protected function getClientParams(array $params): array
    {
        return [
            // Default highest number, since we'll consolidate later,
            // let's grab everything we can get our hands on!
            'maxResults' => 2500,
            // Whatever, we'll sort later
            'orderBy' => 'startTime',
            // Do not change this shit, seriously.
            'singleEvents' => true,
            // yeah, let's filter a bit
            // @todo maybe don't, depends, lets look into that
            'timeMin' => date('c', $params['start']),
            'timeMax' => date('c', $params['end']),
        ];
    }

    /**
     * @return array
     */
    protected function getSources(): array
    {
        return $this->sources;
    }

    /**
     * @param array $sources
     */
    protected function setSources(array $sources): void
    {
        // @todo properly validate this
        $this->sources = $sources;
    }

    /**
     * @return string
     */
    protected function getAuthConfig(): string
    {
        return $this->authConfig;
    }

    /**
     * Sets the Auth Config for Client
     *
     * @param string $authConfig
     * @throws \InvalidArgumentException
     */
    protected function setAuthConfig(string $authConfig): void
    {
        if (!file_exists($authConfig)) {
            throw new \InvalidArgumentException('File not found!');
        }
        $this->authConfig = $authConfig;
    }

    /**
     * @return \Google_Service_Calendar
     */
    protected function getService($settings): \Google_Service_Calendar{
        if ($this->client === null) {
            try {
                $this->client = new \Google_Client();
                $this->client->setAuthConfig($settings[self::class]['credentials']);
                $this->client->setScopes([\Google_Service_Calendar::CALENDAR_READONLY]);
            } catch (\Exception $e) {
                //@todo do something here, maybe logging?
            }
        }
        return new \Google_Service_Calendar($this->client);
    }

    /**
     * @param array $sources
     * @param array $params
     * @param array $settings
     * @param EventCollection $collection
     * @param EventLabeler $labelPrinter
     * @param null|LoggerInterface $logger
     */
    public function fetchEvents(
        array $sources,
        array $params,
        array $settings,
        EventCollection $collection,
        EventLabeler $labelPrinter,
        ?LoggerInterface $logger = null
    ): void {
        foreach ($sources as $source) {
            $optParams = $this->getClientParams($params);
            $gService = $this->getService($settings);
            try {
                $gEvents = $gService->events->listEvents($source, $optParams);
            } catch (\Exception $e) {
                // @todo do something fancy here with errors, logging and shit.
            }
            /** @var \Google_Service_Calendar_Event $nextEvent */
            while ($nextEvent = $gEvents->next()) {

                    $eventStart = $nextEvent->getStart();
                    $eventEnd = $nextEvent->getEnd();
                    $collection->addToCollection(
                        new Event(
                            (int) strtotime($eventStart->getDateTime() ?? $eventStart->getDate()) ,
                            (int) strtotime($eventEnd->getDateTime() ?? $eventEnd->getDate()),
                            (string) $nextEvent->getLocation(),
                            (string) $nextEvent->getId(),
                            (string) $nextEvent->getHtmlLink(),
                            (string) $nextEvent->getSummary(),
                            $labelPrinter->getLabelsForSource($source),
                            (string) $nextEvent->getDescription()
                        )
                    );



            }
        }
    }

}
