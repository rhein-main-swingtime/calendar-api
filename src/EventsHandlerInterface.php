<?php
declare(strict_types=1);
/**
 * DOCBLOCKSTUFF
 * @author: mfk
 */

namespace rmswing;


interface EventsHandlerInterface
{

    public const SORT_ORDER_ASC = 'ascending';
    public const SORT_ORDER_DESC = 'descencing';
    public const DEFAULT_SORT_ORDER = self::SORT_ORDER_ASC;
    public const VALID_ORDERS = [
        self::SORT_ORDER_ASC, self::SORT_ORDER_DESC,
    ];


    // Paging
    /** @var bool turns paging on and off */
    public const DEFAULT_PAGING = false;
    public const VALID_PAGING_VALUES = [true, false];
    /** @var int sets length of paging */
    public const DEFAULT_PAGE_LENGTH = 5;

    /** @var int If < 1 All events in Date Range will be given back */
    public const DEFAULT_LIMIT = 0;

    /** @var string */
    public const DEFAULT_START_DATE = 'now';
    public const DEFAULT_END_DATE = '+6 months';

    // Currently running event
    /** @var bool turns paging on and off */
    public const DEFAULT_GET_CURRENTLY_RUNNING = true;
    public const VALID_GET_CURRENTLY_RUNNING = [true, false];

    public const ALLOWED_PARAMETERS = [

    ];

}
