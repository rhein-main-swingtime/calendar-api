<?php

use Respect\Validation\Validator as v;
use rmswing\EventsController;

/**
 * Slim App configs
 * @author: mfk
 */

$config = [];
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['EventsRoute'] = [
    // Source dependend settings
    rmswing\eventsources\Google::class => [
        'credentials' =>    __DIR__ . DIRECTORY_SEPARATOR
                            . '..' . DIRECTORY_SEPARATOR
                            . 'credentials' . DIRECTORY_SEPARATOR
                            . 'google_credentials.json'
    ],
    // These labels will be returned in the response for each indiviual event.
    // uf an eventsource has a category matching one of these labels
    'category_labels' => [
        'class' => 'Class Time',
        'social' => 'Social Time'
    ],
    // Sources used to fetch events
    'sources'=> [
        [
            // RMSwing Class Time
            'id' => 'nnmbsl8464ifogcl3ld3lr1cs8@group.calendar.google.com',
            'handler' => rmswing\eventsources\Google::class,
            'categories' => ['class', 'all'],
        ],
        [
            // RMSwing Social Time
            'id' => 's1nbnfmv1lnfc013iuuqg94oo8@group.calendar.google.com',
            'handler' => rmswing\eventsources\Google::class,
            'categories' => ['social', 'all'],
        ]
    ],
    'validations' => [
        EventsController::CATEGORY_PARAMETER        => v::optional(
            v::in(EventsController::CATEGORY_VALID)
        ),
        EventsController::END_DATE_PARAMETER        => v::optional(v::numeric()->positive()),
        EventsController::PAGE_SIZE_PARAMETER       => v::optional(v::numeric()->positive()),
        EventsController::PAGING_PARAMETER          => v::optional(v::boolVal()),
        EventsController::SORT_ORDER_PARAMETER      => v::optional(
            v::in(EventsController::SORT_ORDER_VALID)
        ),
        EventsController::START_DATE_PARAMETER      => v::optional(v::numeric()->positive()),
    ]
];
