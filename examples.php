<?php

/**
 * Flat.php examples
 * https://thenocoder.github.io/FlatPHP/
 * Copyright (c) 2023
 * https://github.com/TheNocoder/FlatPHP/LICENSE
 */

include 'Flat.php';

/*
 * Default options
 */
$flattened   = [];
$unflattened = [];
$sampleArray = [
    'id'         => 12345,
    'name'       => 'A name',
    'properties' => [
        'type' => 'A type',
        'geo'  => [
            'latitude'  => 12.3456,
            'longitude' => 12.3456
        ],
        'collections' => [
            [true, false],
            ['a', 'b']
        ]
    ],
    'empty' => [[]],
];

echo "<pre>";

echo "\nDefault options.";
flatten_array($sampleArray, $flattened);
unflatten_array($flattened, $unflattened);
echo "\nsampleArray to flattened:\n";
print_r($flattened);
echo "\nflattened to JSON:\n";
print_r(json_encode($flattened, JSON_PRETTY_PRINT));
echo "\n";
echo "\nflattened to unflattened:\n";
print_r($unflattened);

/*
 * Custom prefix for suffix
 */
$flattened   = [];
$unflattened = [];
$options     = [
    'prefix'          => '{',    // prefix
    'suffix'          => '}',    // suffix
    'suffix-end'      => true,   // ends with $suffix
    'prefix-list'     => '[',    // prefix if array list
    'suffix-list'     => ']',    // suffix if array list
    'suffix-list-end' => true,   // ends with $sufixList if array list
];
echo "\nflattened by options:\n";
print_r($options);
flatten_array($sampleArray, $flattened, $options);
print_r($flattened);

/*
 * Custom suffix in all cases
 */
$flattened   = [];
$unflattened = [];
$options     = [
    'prefix'      => '',
    'suffix'      => '->',
    'suffix-end'  => false,
    'prefix-list' => '',
    'suffix-list' => '',
];
echo "\nflattened by options:\n";
print_r($options);
flatten_array($sampleArray, $flattened, $options);
print_r($flattened);

/*
 * Custom start
 */
$flattened   = [];
$unflattened = [];
$start       = 'https://example.com/';
$options     = [
    'prefix'      => '',
    'suffix'      => '/',
    'suffix-end'  => true,
    'prefix-list' => '',
    'suffix-list' => '',
];
echo "\nflattened \$start='$start' by options:\n";
print_r($options);
flatten_array($sampleArray, $flattened, $options, $start);
print_r($flattened);

/*
 * Performance
 */
$flattened   = [];
$unflattened = [];
$inter       = 1000;

$start = microtime(true);
for ($n = 0; $n < $inter; $n++) {
    flatten_array($sampleArray, $flattened);
}
$end = microtime(true);
echo "\nPerformance flatten_array ($inter interactions):\n";
echo ($end - $start) * 1000;
echo " ms.\n";

$start = microtime(true);
for ($n = 0; $n < $inter; $n++) {
    unflatten_array($flattened, $unflattened);
}
$end = microtime(true);
echo "\nPerformance unflatten_array ($inter interactions):\n";
echo ($end - $start) * 1000;
echo " ms.\n";

echo "</pre>";
