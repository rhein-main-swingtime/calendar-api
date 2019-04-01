<?php
declare(strict_types=1);

/**
 * DOCBLOCKSTUFF
 * @author: mfk
 */

namespace rmswing;


/**
 * Event Class represents a generic event.
 *
 * These are the building blocks given back to a frontend
 * @package rmswing
 */
class Event implements \JsonSerializable
{
    /**
     * @var int
     */
    protected $startTime;
    /**
     * @var int
     */
    protected $endTime;
    /**
     * @var string
     */
    protected $startTimeRFC;
    /**
     * @var string
     */
    protected $endTimeRFC;

    /**
     * @var string
     */
    protected $location;
    /**
     * @var string
     */
    protected $SourceEventId;
    /**
     * @var string
     */
    protected $htmlLink;
    /**
     * @var string
     */
    protected $summary;
    /**
     * @var string
     */
    protected $categories;
    /**
     * @var string
     */
    protected $description;
    /**
     * @var bool
     */
    protected $happensRightNow = false;

    /**
     * Event constructor.
     * @param int|null $startTime
     * @param int|null $endTime
     * @param null|string $location
     * @param null|string $id
     * @param null|string $htmlLink
     * @param null|string $summary
     * @param array|null $categories
     * @param null|string $description
     */
    public function __construct(
        ?int $startTime,
        ?int $endTime,
        ?string $location,
        ?string $id,
        ?string $htmlLink,
        ?string $summary,
        ?array $categories,
        ?string $description
    ) {
        $this->setTime($startTime, 'startTime');
        $this->setTime($endTime, 'endTime');
        $this->location = $location;
        $this->SourceEventId =$id;
        $this->htmlLink = $htmlLink;
        $this->summary = $summary;
        $this->categories = $categories;
        $this->description = $description;
    }

    /**
     * @param $prop
     * @return mixed
     */
    public function __get($prop)
    {
        return $this->$prop;
    }

    /**
     * @param $prop
     * @return bool
     */
    public function __isset($prop) : bool
    {
        return isset($this->$prop);
    }

    /**
     * @param int $time
     */
    public function isCurrent(int $time): void {
        if ($this->getStartTime() <= $time && $this->getEndTime() >= $time ){
            $this->happensRightNow = true;
        } else {
            $this->happensRightNow = false;
        }
    }

    /**
     * @param $time
     * @param string Field to set, can be 'end' or 'start'
     */
    public function setTime($time, string $field): void
    {
        if (!in_array($field, ['endTime', 'startTime'])){
            throw new \InvalidArgumentException('$field can only be "end" or "start"');
        }

        if (gettype($time) === 'integer') {
            $this->{$field . 'RFC'} = date('c', $time);
            $this->{$field} = $time;
        } else {
            $this->{$field} = strtotime($time);
            $this->{$field . 'RFC'} = $time;
        }

        $diff = $this->{$field} - strtotime($this->{$field . 'RFC'});
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        $out = [];
        foreach ($this as $i => $v) {
            $out[$i] =  $v;
        }
        return $out;
    }

    /**
     * @return int
     */
    public function getStartTime(): int
    {
        return $this->startTime;
    }

    /**
     * @return int
     */
    public function getEndTime(): int
    {
        return $this->endTime;
    }

    /**
     * @return string
     */
    public function getStartTimeRFC(): string
    {
        return $this->startTimeRFC;
    }

    /**
     * @return string
     */
    public function getEndTimeRFC(): string
    {
        return $this->endTimeRFC;
    }

    /**
     * @return string
     */
    public function getCategories(): string
    {
        return $this->categories;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @return string|null
     */
    public function getSourceEventId(): ?string
    {
        return $this->SourceEventId;
    }

    /**
     * @return string|null
     */
    public function getHtmlLink(): ?string
    {
        return $this->htmlLink;
    }

    /**
     * @return string|null
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @return string|null
     */
    public function getCategory(): ?string
    {
        return $this->categories;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
