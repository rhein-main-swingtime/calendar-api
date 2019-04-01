<?php
declare(strict_types=1);

/**
 * DOCBLOCKSTUFF
 * @author: mfk
 */

namespace rmswing;

use rmswing\EventParametersInterface;

/**
 * Class EventCollection
 * @package rmswing
 */
class EventCollection implements EventParametersInterface
{

    /** @var Event[] $events */
    protected $events = [];

    /**
     * @var string
     */
    protected $sortedDirection = '';

    /**
     * @return EventCollection
     */
    public static function create()
    {
       return new self();
    }


    /**
     * @param Event $event
     * @return string
     */
    private function genUniqueID(Event $event): string
    {
        return str_replace(
            '.',
            '_',
            uniqid(
                md5($event->getSourceEventId()) . '_',
                true
            )
        );
    }

    /**
     * @param Event $event
     */
    public function addToCollection(Event $event): void
    {
        $id = $this->genUniqueID($event);
        $this->events[$id] = $event;
    }

    /**
     * Sets the sort order for the event collection
     *
     * @param string $sortOrder
     */
    public function setSortOrder(string $sortOrder): void
    {
        if (!in_array($sortOrder, self::SORT_ORDER_VALID)) {
            throw new \InvalidArgumentException("$sortOrder is not allowed");
        }

        $this->sortOrder = $sortOrder;
    }


    /**
     * Enables paging for the collection
     * @param bool $paging
     */
    public function enablePaging(bool $paging): void
    {
        $this->pageingEnabled = $paging;
    }

    /**
     * @param bool $currentlyRunning
     */
    public function enableCurrentlyRunning(bool $currentlyRunning): void
    {
        $this->currentlyRunning = $currentlyRunning;
    }

    /**
     * @param int $pageLength
     */
    public function setPageLength(int $pageLength): void
    {
        if ($pageLength < 1) {
            throw new \InvalidArgumentException('$pageLength must be bigger than zero.');
        }
        $this->pageLength = $pageLength;
    }

    /**
     * @param string $startDate
     */
    public function setStartDate(string $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @param string $endDate
     */
    public function setEndDate(string $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return Event[]
     */
    public function getEvents(
        string $order,
        $startTime,
        $endTime,
        bool $paging,
        ?int $pageLength,
        ?int $limit,
        ?int $offset
    ): array {

        if (gettype($startTime) === 'string') {
            $startTime = strtotime($startTime);
        }

        if (gettype($endTime) === 'string') {
            $endTime = strtotime($endTime);
        }


        $out['events'] = $this->getSortedCollection(
            $order,
            $startTime,
            $endTime,
            $limit,
            $offset
        );

        // @todo find a more elegant solution
        if ($paging === true) {
            $out = $this->getEventPages($out, $pageLength);
        }

        return $out;
    }

    /**
     * @param string $order
     * @param $startDate
     * @param $endDate
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    private function getSortedCollection(
        string $order,
        $startDate,
        $endDate,
        ?int $limit,
        ?int $offset
    ): array
    {
        $sortedCollection = array_filter(
            $this->events,
            function($element) use ($startDate, $endDate) {
                /** @var Event $element */
                return (
                    $element->getStartTime() >= $startDate
                    && $element->getEndTime() <= $endDate
                );
            }
        );

        uasort(
            $sortedCollection,
            function ($a, $b) use ($order) {
                /** @var Event $a */
                /** @var Event $b */
                $aTime = $a->getStartTime();
                $bTime = $b->getStartTime();

                if ($order === self::SORT_ORDER_ASC) {
                    return $aTime - $bTime;
                }
                return $bTime - $aTime;
            }
        );

        if ($limit !== null || $offset !== null) {
            $sortedCollection = array_slice(
                $sortedCollection,
                $offset ?? 0,
                $limit
            );
        }

        $now = time() + 15 * 60; // 15 Minutes because nobody ever is on time...

        foreach ($sortedCollection as $ele) {
            /** @var Event $ele */
            $ele->isCurrent($now);
        }

        return $sortedCollection;
    }

    /**
     * @param array $collection
     * @param int $pageLength
     * @return array
     */
    private function getEventPages(
        array $collection,
        int $pageLength
    ): array
    {

        $now = time() + 15 * 60 ; // We'll show events that actually start in 15 minutes as currently running.
        $collection['pages'] = array_chunk(
            array_keys($collection['events']),
            $pageLength, false
        );

        return $collection;
    }

    /**
     * @param EventCollection $collection
     */
    public function addCollectionToCollection(EventCollection $collection): void
    {
        foreach ($collection->getEvents() as $event) {
            $this->addToCollection($event);
        }
    }

    /**
     * @param mixed ...$collections
     */
    public function addCollectionsToCollection(...$collections)
    {
        foreach ($collections as $collection) {
            /** @var EventCollection $collection */
            //$this->events[] = $collection->getEvents();
            foreach ($collection->getEvents() as $event) {
                $this->addToCollection($event);
            }
        }
    }

}
