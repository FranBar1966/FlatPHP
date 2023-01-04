<?php

/**
 * Flat.php
 * https://thenocoder.github.io/FlatPHP/
 * Copyright (c) 2023
 * https://github.com/TheNocoder/FlatPHP/LICENSE
 */

/**
 * array_is_list only in PHP >= 8.1
 */
if (!function_exists('array_is_list')) {
    function array_is_list(array $array)
    {
        return $array === [] || (array_keys($array) === range(0, count($array) - 1));
    }
}

/**
 * Flattens an array.
 *
 * Example:
 *
 * $flatt = [];
 * $start = '$';
 * $array = [
 *     'assokey' => ['Foo'],
 * ];
 * $options = [
 *     'prefix'          => '{',
 *     'suffix'          => '}',
 *     'suffix-end'      => true,
 *     'prefix-list'     => '[',
 *     'suffix-list'     => ']',
 *     'suffix-list-end' => true,
 * ];
 *
 * Result of
 * flatten_array($array, $flatt, $options, $start)
 *
 * $flatt = [
 *     '${assokey}[0]' => 'Foo',
 * ];
 *
 * .-------------> $start param
 * |.------------> 'prefix'
 * ||       .----> 'suffix' only if 'suffix-end' is true
 * ||       |.---> 'prefix-list'
 * ||       || .-> 'suffix-list' only if 'suffix-list-end' is true
 * ||       || |
 * ${assokey}[0] => 'Foo'
 *
 * @param array  $source        Array to flatten
 * @param array  &$destination  Array to fill with flattened
 * @param array  $options
 * @param string $start
 * @return void
 */
function flatten_array($source, &$destination, $opt = [], $start = '')
{
    $opt['prefix']          ??= '';     // prefix
    $opt['suffix']          ??= '.';    // suffix
    $opt['suffix-end']      ??= false;  // ends with $suffix
    $opt['prefix-list']     ??= '[';    // prefix if array list
    $opt['suffix-list']     ??= ']';    // suffix if array list
    $opt['suffix-list-end'] ??= true;   // ends with $sufixList if array list

    if (($opt['prefix-list'] || $opt['suffix-list']) && array_is_list($source)) {
        if (!$opt['suffix-end']) {
            // deduplicate suffix
            $start = rtrim($start, $opt['suffix']);
        }
        $currentPrefix    = $opt['prefix-list'];
        $currentSuffix    = $opt['suffix-list'];
        $currentSuffixEnd = $opt['suffix-list-end'];
    } else {
        $currentPrefix    = $opt['prefix'];
        $currentSuffix    = $opt['suffix'];
        $currentSuffixEnd = $opt['suffix-end'];
    }

    $currentName = $start;

    foreach ($source as $key => $val) {
        $currentName .= $currentPrefix.$key;

        if (is_array($val) && !empty($val)) {
            $currentName .= "$currentSuffix";
            flatten_array($val, $destination, $opt, $currentName);
        } else {
            if ($currentSuffixEnd) {
                $currentName .= $currentSuffix;
            }
            $destination[$currentName] = $val;
        }

        $currentName = $start;
    }
}

/**
 * Unflatten an flattened array.
 *
 * (!) Same options used for flattening are necessary.
 *
 * @param array  $source        Flattened array to unflatten
 * @param array  &$destination  Array to fill with unflatten
 * @param array  $options
 * @param string $start
 * @return void
 */
function unflatten_array($source, &$destination, $opt = [], $start = '')
{
    $opt['prefix']          ??= '';
    $opt['suffix']          ??= '.';
    $opt['suffix-end']      ??= false; // This option is IGNORED for unflatten.
    $opt['prefix-list']     ??= '[';
    $opt['suffix-list']     ??= ']';
    $opt['suffix-list-end'] ??= true;  // This option is IGNORED for unflatten.

    $splitter = $opt['suffix'] ?: $opt['prefix'] ?: $opt['prefix-list'] ?: $opt['suffix-list'];
    $pattern  = '[\\' . ($opt['suffix'] ?: $splitter) . '\\' . ($opt['prefix'] ?: $splitter) . '\\' . ($opt['prefix-list'] ?: $splitter) . '\\' . ($opt['suffix-list'] ?: $splitter) . ']+';

    foreach ($source as $key => $val) {
        $key = ltrim($key, $start);
        $key = preg_replace("/$pattern/", $splitter, $key);
        $key = trim($key, $splitter);
        $sub = explode($splitter, $key);
        $ref = &$destination;

        foreach ($sub as $subkey) {
            if (!isset($ref[$subkey])) {
                $ref[$subkey] = [];
            }

            $ref = &$ref[$subkey];
        }

        $ref = $val;
    }
}
