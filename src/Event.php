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

    public const AVAILABLE_PROPERTIES = [
        'startTime', 'endTime', 'location', 'id',
        'htmlLink', 'summary', 'category', 'description'
    ];

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
    protected $location = '';
    /**
     * @var string
     */
    protected $SourceEventId = '';
    /**
     * @var string
     */
    protected $htmlLink = '';
    /**
     * @var string
     */
    protected $summary = '';
    /**
     * @var string
     */
    protected $category = '';
    /**
     * @var string
     */
    protected $description = '';

    public function __construct(
        ?string $startTime,
        ?string $endTime,
        ?string $location,
        ?string $id,
        ?string $htmlLink,
        ?string $summary,
        ?string $category,
        ?string $description
    ) {
        $this->setStartTime($startTime);
        $this->setEndTime($endTime);
        $this->location = $location;
        $this->SourceEventId =$id;
        $this->htmlLink = $htmlLink;
        $this->summary = $summary;
        $this->category = $category;
        $this->description = $description;
    }

    public function jsonSerialize()
    {
        $out = [];

        foreach ($this as $i => $v) {
            $out[$i] =  $v; // @todo check wether this is actually valid
        }

        return $out;

    }

    /**
     * This is needed, since'll convert the time to a unix timestamp
     *
     * @param string Start time
     * @return void
     * @throws \InvalidArgumentException
     */
    private function setStartTime(?string $time): void
    {

        $startTime = strtotime($time);

        if ($startTime === false) {
            throw new \InvalidArgumentException('Start is not convertible to a unix time stamp');
        }

        $this->startTime = $startTime;
    }

    /**
     * @return string|null
     */
    public function getStartTime(): int
    {
        // @todo implement multiple formats
        return $this->startTime;
    }

    /**
     * This is needed, since'll convert the time to a unix timestamp
     *
     * @param string End time
     * @return void
     * @throws \InvalidArgumentException
     */
    private function setEndTime(string $time): void
    {

        $endTime = strtotime($time);

        if ($endTime === false) {
            throw new \InvalidArgumentException('End is not convertible to a unix time stamp');
        }

        $this->endTime = $endTime;
    }

    /**
     * @return string|null
     */
    public function getEndTime(): int
    {
        // @todo implement multiple formats
        return $this->endTime;
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
        return $this->category;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getAsArray(): array
    {
        $out = [];
        foreach (self::AVAILABLE_PROPERTIES as $property) {
            $out['property'] = $this->{$property};
        }
        return $out;
    }
}
