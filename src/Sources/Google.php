<?php
declare(strict_types=1);


namespace rmswing\sources;

use rmswing\Event;
use rmswing\EventCollection;

/**
 * DOCBLOCKSTUFF
 * @author: mfk
 */
class Google
{
    protected const OPT_PARAMS = [
        'maxResults' => 10,
        'orderBy' => 'startTime',
        'singleEvents' => true,
        //'timeMin' => date('c'),
    ];

    /** @var Google_Client|null */
    protected $client;

    /** @var array */
    protected $sources;

    /** @var string */
    protected $authConfig;

    public function __construct(
        array $sources,
        array $settings
    ) {
        $this->setAuthConfig($settings['credentials']);
        $this->setSources($sources);
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
    protected function getService(): \Google_Service_Calendar{
        if ($this->client === null) {
            try {
                $this->client = new \Google_Client();
                $this->client->setAuthConfig($this->getAuthConfig());
                $this->client->setScopes([\Google_Service_Calendar::CALENDAR_READONLY]);
            } catch (\Exception $e) {
                //@todo do something here, maybe logging?
            }
        }
        return new \Google_Service_Calendar($this->client);
    }

    public function getEvents(): EventCollection
    {

        $out = new EventCollection();

        foreach ($this->getSources() as $source) {
            $optParams = array_merge(self::OPT_PARAMS, ['timeMin' => date('c')]);
            $gService = $this->getService();
            $gEvents = $gService->events->listEvents($source, $optParams);

            /** @var \Google_Service_Calendar_Event $nextEvent */
            while ($nextEvent = $gEvents->next()) {
                $out->addToCollection(
                    new Event(
                        (string) $nextEvent->getStart()->getDateTime(),
                        (string) $nextEvent->getEnd()->getDateTime(),
                        (string) $nextEvent->getLocation(),
                        (string) $nextEvent->getId(),
                        (string) $nextEvent->getHtmlLink(),
                        (string) $nextEvent->getSummary(),
                        'Something useful here!', // @todo fix this
                        (string) $nextEvent->getDescription()
                    )
                );
            }
        }

        return $out;
    }

}
