<?php
declare(strict_types=1);

/**
 * DOCBLOCKSTUFF
 * @author: mfk
 */

namespace rmswing;


interface EventParametersInterface
{

    /**
     * Public parameters
     */

    // Sorting
    public const SORT_ORDER_PARAMETER   = 'order';
    public const SORT_ORDER_ASC         = 'asc';
    public const SORT_ORDER_DESC        = 'desc';
    public const SORT_ORDER_DEFAULT     = self::SORT_ORDER_ASC;
    public const SORT_ORDER_VALID       = [
        self::SORT_ORDER_ASC, self::SORT_ORDER_DESC
    ];

    // Start/End Date
    public const START_DATE_DEFAULT     = 'now';
    public const START_DATE_PARAMETER   = 'start';
    public const END_DATE_PARAMETER     = 'end';
    public const END_DATE_DEFAULT       = '+6 months';

    // Offset/Limit
    public const OFFSET_PARAMETER = 'offset';
    public const LIMIT_PARAMETER  = 'limit';

    // Paging
    public const PAGING_PARAMETER   = 'paging';
    public const PAGING_ENABLED     = 'true';
    public const PAGING_DISABLED    = '';
    public const PAGING_DEFAULT     = self::PAGING_ENABLED;
    public const PAGING_VALID       = [
        self::PAGING_ENABLED, self::PAGING_DISABLED
    ];

    public const PAGE_SIZE_PARAMETER = 'pageSize';
    public const PAGE_SIZE_DEFAULT = '5';

    // Category
    public const CATEGORY_SOCIAL    = 'social';
    public const CATEGORY_CLASS     = 'class';
    public const CATEGORY_ALL       = 'all';
    public const CATEGORY_PARAMETER = 'category';
    public const CATEGORY_PARAMETER_URL = '[{' . self::CATEGORY_PARAMETER . '}]';
    public const CATEGORY_DEFAULT   = self::CATEGORY_ALL;
    public const CATEGORY_VALID     = [
        self::CATEGORY_ALL, self::CATEGORY_CLASS, self::CATEGORY_SOCIAL
    ];

    // Currently running
    public const CURRENTLY_RUNNING_ENABLED  = 'true';
    public const CURRENTLY_RUNNING_DISABLED = '';
    public const CURRENTLY_RUNNING_DEFAULT  = self::CURRENTLY_RUNNING_DISABLED;
    public const CURRENTLY_RUNNING_PARAMETER = 'currentlyRunning';

}
