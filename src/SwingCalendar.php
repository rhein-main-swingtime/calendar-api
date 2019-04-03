<?php
declare(strict_types=1);

/**
 * DOCBLOCKSTUFF
 * @author mafeka <felix@kakrow.me>
 */

namespace rmswing;


use ICal\ICal;

class SwingCalendar extends ICal
{

    /**
     * Define which variables can be configured
     *
     * @var array
     */
    private static $configurableOptions = array(
        'defaultSpan',
        'defaultTimeZone',
        'defaultWeekStart',
        'disableCharacterReplacement',
        'filterDaysAfter',
        'filterDaysBefore',
        'replaceWindowsTimeZoneIds',
        'skipRecurrence',
        'useTimeZoneWithRRules',
        'calenderCategory'
    );

    public function __construct($files = false, array $options = array())
    {
        parent::__construct($files, $options);
    }

}
