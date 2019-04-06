<?php
declare (strict_types = 1);

namespace rmswing;

use rmswing\EventParametersInterface;

/**
 * Class EventCollection
 * @author mafeka https://github.com/mafeka
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

    /** @var string */
    protected $sortOrder = '';

    /**
     * @return EventCollection
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @param Event $event
     */
    public function addToCollection(Event $event): void
    {
        // $eventId = $this->genUniqueID($event);
        // $this->events[$eventId] = $event;
        $this->events[] = $event;
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
 * Returns sorted array of events
 *
 * @param string        $order      Order, see self::SORT_ORDER_VALID
 * @param mixed         $startTime  Can be either RFC3223 formated or unix timestamp
 * @param mixed         $endTime    Can be either RFC3223 formated or unix timestamp
 * @param string        $listMode   List mode, see self::EVENT_LIST_MODE_VALID
 * @param integer|null  $limit      Limit of events
 * @param integer|null  $offset     Offset
 * @return array
 */
    public function getEvents(
        string $order,
        $startTime,
        $endTime,
        string $listMode,
        ?int $limit,
        ?int $offset
    ): array {

        if (is_string($startTime)) {
            $startTime = strtotime($startTime);
        }

        if (is_string($endTime)) {
            $endTime = strtotime($endTime);
        }

        $out['events'] = $this->getSortedCollection(
            $order,
            $startTime,
            $endTime,
            $listMode,
            $limit,
            $offset
        );

        return $out;
    }

    /**
     * @param string    $order
     * @param mixed     $startDate
     * @param mixed     $endDate
     * @param int|null  $limit
     * @param int|null  $offset
     * @return array
     */
    private function getSortedCollection(
        string $order,
        $startDate,
        $endDate,
        string $listMode,
        ?int $limit,
        ?int $offset
    ): array {
        $sortedCollection = array_filter(
            $this->events,
            function ($element) use ($startDate, $endDate) {
                /** @var Event $element */

                // Never repeat yourself, Dummy! Repeat vars endlessly instead!
                $eventStartTime = $element->getStartTime();
                $eventEndTime   = $element->getEndTime();

                $diff = $startDate - $eventStartTime;

                if ($eventStartTime >= $startDate
                    && $eventStartTime <= $endDate) {
                        // Event starts after the searched for start date
                        // AND event ends before the searched-for end date
                        return true;
                } elseif ($eventStartTime < $startDate
                          && $eventEndTime > $startDate) {
                         // Event has already started
                         // AND event is ends after searched-for start date
                         return true;
                } elseif ($eventStartTime >= $startDate
                          && $eventEndTime > $endDate) {
                        // Event starts after searched-for start date
                        // AND event ends after searched-for end date
                        return true;
                }

                // all other cases:
                return false;
            }
        );

        usort(
            $sortedCollection,
            function ($first, $second) use ($order) {
                /** @var Event $first */
                /** @var Event $second */
                $firstTime = $first->getStartTime();
                $secondTime = $second->getStartTime();

                if ($order === self::SORT_ORDER_ASC) {
                    return $firstTime - $secondTime;
                }
                return $secondTime - $firstTime;
            }
        );

        if (($limit !== null && $limit > 0) || $offset !== null) {
            $sortedCollection = array_slice(
                $sortedCollection,
                $offset ?? 0,
                ($limit > 0 ? $limit : null)
            );
        }

        $now = time() + 15 * 60; // 15 Minutes because nobody ever is on time...

        foreach ($sortedCollection as $ele) {
            /** @var Event $ele */
            $ele->isCurrent($now);
        }

        if ($listMode === self::EVENT_LIST_MODE_CALENDAR) {
            $sortedCollection = $this->getCalendarizedList($sortedCollection);
        }

        return $sortedCollection;
    }


    private function getCalendarizedList(array $collection): array
    {
        $out = [];
        foreach ($collection as $event) {
            /** @var \rmswing\Event $event */
            $formatedDate = date('Y-m-d', $event->getStartTime());
            if (!array_key_exists($formatedDate, $out)) {
                $out[$formatedDate] = [];
            }
            $out[$formatedDate][] = $event;
        }

        return $out;
    }

    /**
     * @param array $collection
     * @param int $pageLength
     * @return array
     * @deprecated Nobody actually needs that shit anyways.
     */
    private function getEventPages(
        array $collection,
        int $pageLength
    ): array {
        $collection['pages'] = array_chunk(
            array_keys($collection['events']),
            $pageLength,
            false
        );
        return $collection;
    }

    /**
     * @param mixed ...$collections
     * @deprecated This seems redundant right now.
     * @todo either fix or remove
     */
    public function addCollectionsToCollection(...$collections)
    {
        // foreach ($collections as $collection) {
        //     /** @var EventCollection $collection */
        //     foreach ($collection->getEvents() as $event) {
        //         $this->addToCollection($event);
        //     }
        // }
    }
}
