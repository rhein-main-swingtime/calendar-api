<?php
declare(strict_types=1);

/**
 * DOCBLOCKSTUFF
 * @author: mfk
 */

namespace rmswing;

class EventCollection implements \JsonSerializable, EventsHandlerInterface
{

    /** @var string */
    protected $sortOrder = self::DEFAULT_SORT_ORDER;

    /** @var bool */
    protected $pagingEnabled = self::DEFAULT_PAGING;

    /** @var int Must be > 0 */
    protected $pageLength = self::DEFAULT_PAGE_LENGTH;

    /** @var string Must be parsable by strtotime() */
    protected $startDate = self::DEFAULT_START_DATE;

    /** @var string Must be parsable by strtotime() */
    protected $endDate = self::DEFAULT_END_DATE;

    /** @var int */
    protected $limit = self::DEFAULT_LIMIT;

    /** @var bool If true, builds an extra page with events currently running */
    protected $currentlyRunning = self::DEFAULT_GET_CURRENTLY_RUNNING;

    /** @var Event[] $events */
    protected $events = [];

    // Methods

    private function genUniqueID(Event $event): string {
        return str_replace('.',
            '_',
            uniqid(
                md5($event->getSourceEventId()) . '_',
                true
            ));
    }

    public function addToCollection(Event $event): void
    {
        $id = $this->genUniqueID($event);
        $this->events[$id] = $event;
    }

    public function jsonSerialize(): array
    {
        $this->sortCollection();

        $out = [];
        $out['events'] = $this->events;

        if ($this->currentlyRunning === true || $this->pagingEnabled === true) {
            $out = array_merge($out, $this->getEventPages());
        }

        return $out;
    }

    /**
     * Sets the sort order for the event collection
     *
     * @param string $sortOrder
     */
    public function setSortOrder(string $sortOrder): void
    {
        if (!in_array($sortOrder, self::VALID_ORDERS)) {
            throw new \InvalidArgumentException("$sortOrder is not allowed");
        }

        $this->sortOrder = $sortOrder;
    }


    /**
     * Enables paging for the collection
     * @param bool $paging
     */
    public function enablePaging(bool $paging = self::DEFAULT_PAGING): void
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
    public function getEvents(): array
    {
        $this->sortCollection();
        return $this->events;
    }

    private function sortCollection(): void
    {
        $order = $this->sortOrder;

        uasort(
            $this->events,
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
    }

    private function getEventPages(): array
    {
        $out = [];
        $buffer = [];

        $now = time() + 15 * 60 ; // We'll show events that actually start in 15 minutes as currently running.
        $this->currentlyRunning;

        foreach ($this->events as $index => $event) {
            /** @var Event $event */
            $startTime = $event->getStartTime();
            $endTime = $event->getEndTime();

            if ($this->currentlyRunning
                && $startTime < $now
                && $endTime > $now) {
                $out['currentlyRunning'][] = $index;
            }

            $buffer[] = $index;
        }

        $out['pages'] = array_chunk($buffer, $this->pageLength, false);

        return $out;
    }

    public function addCollectionToCollection(EventCollection $collection): void
    {
        foreach ($collection->getEvents() as $event) {
            $this->addToCollection($event);
        }
    }

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
