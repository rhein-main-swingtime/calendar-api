<?php
/**
 * DOCBLOCKSTUFF
 * @author: mfk
 */

$config = [];
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;


$config['events']['defaults'] = [
  'paging'  => 15,
  'sort'    => 'startDate',
  'sortOrder' => ''
];
$config['events'][rmswing\sources\Google::class] = [
    'credentials' => __DIR__ . DIRECTORY_SEPARATOR . 'google_credentials.json'
];
$config['events']['badges'] = [ // @Todo, not elegant, better solution?
  'Class Time' => ['RheinMain Swing Class Time'],
  'Social Time' => ['RheinMain Swing Social Time'],
];
$config['events']['sources'] = [
    [
        // RMSwing Class Time
        'id' => 'nnmbsl8464ifogcl3ld3lr1cs8@group.calendar.google.com',
        'handler' => rmswing\sources\Google::class,
        'categories' => ['classes', 'all'],
    ],
    [
        // RMSwing Social Time
        'id' => 's1nbnfmv1lnfc013iuuqg94oo8@group.calendar.google.com',
        'handler' => rmswing\sources\Google::class,
        'categories' => ['social', 'all'],
    ]
];

$config['events']['parameters'] = [
    'startDate'     => 'date',
    'endDate'       => 'date',
    'limit'         => 'integer',
    'offset'        => 'integer',
    'search'        => 'string',
    'city'          => 'string',
];
