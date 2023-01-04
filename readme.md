Flatten / unflatten PHP arrays (or JSON)
========================================

Utility functions to flatten multidimensional arrays into a one-dimensional array, preserving key names and joining them with customizable separators.

Although PHP makes no special distinction between associative arrays and lists, by default the function allows to distinguish them with different separators for the exchange via JSON and others that do distinguish. In any case it is configurable.

```php
# lists distinction
foo.bar[0] = 'Foo'

# lists ignore
foo.bar.0 = 'Foo'
```

## Usage (flatten)
```php
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

    ...
```

## Usage (unflatten)
```php
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

    ...
```

## Examples

### With the default options

```php
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

// Flatten
flatten_array($sampleArray, $flattened);

RESULT:
(
    [id] => 12345
    [name] => A name
    [properties.type] => A type
    [properties.geo.latitude] => 12.3456
    [properties.geo.longitude] => 12.3456
    [properties.collections[0][0]] => 1
    [properties.collections[0][1]] =>
    [properties.collections[1][0]] => a
    [properties.collections[1][1]] => b
    [empty[0]] => Array()
)

// Flatten to JSON
json_encode($flattened, JSON_PRETTY_PRINT);

RESULT:
{
    "id": 12345,
    "name": "A name",
    "properties.type": "A type",
    "properties.geo.latitude": 12.3456,
    "properties.geo.longitude": 12.3456,
    "properties.collections[0][0]": true,
    "properties.collections[0][1]": false,
    "properties.collections[1][0]": "a",
    "properties.collections[1][1]": "b",
    "empty[0]": []
}

// Unflatten previously flattened array
unflatten_array($flattened, $unflattened);

RESULT:
(
    [id] => 12345
    [name] => A name
    [properties] => Array
        (
            [type] => A type
            [geo] => Array
                (
                    [latitude] => 12.3456
                    [longitude] => 12.3456
                )
            [collections] => Array
                (
                    [0] => Array
                        (
                            [0] => 1
                            [1] =>
                        )
                    [1] => Array
                        (
                            [0] => a
                            [1] => b
                        )
                )
        )
    [empty] => Array
        (
            [0] => Array()
        )
)

```

### Custom prefix and suffix
```php
$options = [
    'prefix'          => '{',    // prefix
    'suffix'          => '}',    // suffix
    'suffix-end'      => true,   // ends with $suffix
    'prefix-list'     => '[',    // prefix if array list
    'suffix-list'     => ']',    // suffix if array list
    'suffix-list-end' => true,   // ends with $sufixList if array list
];

// Flatten
flatten_array($sampleArray, $flattened, $options);

RESULT:
(
    [{id}] => 12345
    [{name}] => A name
    [{properties}{type}] => A type
    [{properties}{geo}{latitude}] => 12.3456
    [{properties}{geo}{longitude}] => 12.3456
    [{properties}{collections}[0][0]] => 1
    [{properties}{collections}[0][1]] =>
    [{properties}{collections}[1][0]] => a
    [{properties}{collections}[1][1]] => b
    [{empty}[0]] => Array()
)

```

### Custom suffix for all cases
```php
$options = [
    'prefix'      => '',
    'suffix'      => '->',
    'suffix-end'  => false,
    'prefix-list' => '',
    'suffix-list' => '',
];

// Flatten
flatten_array($sampleArray, $flattened, $options);

RESULT:
(
    [id] => 12345
    [name] => A name
    [properties->type] => A type
    [properties->geo->latitude] => 12.3456
    [properties->geo->longitude] => 12.3456
    [properties->collections->0->0] => 1
    [properties->collections->0->1] =>
    [properties->collections->1->0] => a
    [properties->collections->1->1] => b
    [empty->0] => Array()
)
```

### Custom options and $start param
```php
$start   = 'https://example.com/';
$options = [
    'prefix'      => '',
    'suffix'      => '/',
    'suffix-end'  => true,
    'prefix-list' => '',
    'suffix-list' => '',
];

// Flatten
flatten_array($sampleArray, $flattened, $options, $start);

RESULT:
(
    [https://example.com/id/] => 12345
    [https://example.com/name/] => A name
    [https://example.com/properties/type/] => A type
    [https://example.com/properties/geo/latitude/] => 12.3456
    [https://example.com/properties/geo/longitude/] => 12.3456
    [https://example.com/properties/collections/0/0/] => 1
    [https://example.com/properties/collections/0/1/] =>
    [https://example.com/properties/collections/1/0/] => a
    [https://example.com/properties/collections/1/1/] => b
    [https://example.com/empty/0/] => Array()
)
```
## Limitations

The key names must not contain the symbols used as separators, doing so will cause problems and make it impossible to unflatten.

This will cause problems:

```php
array = [
    'problem.in.key.name' => 'In value. no problem.',
    'options' => [
        'a' => 1,
        'b' => 2,
    ],
];

// Flatten:
(
    [problem.in.key.name] => In value. no problem.
    [options.a] => 1
    [options.b] => 2
)

// Unflatten:
(
    [problem] => Array
        (
            [in] => Array
                (
                    [key] => Array
                        (
                            [name] => In value. no problem.
                        )
                )
        )
    [options] => Array
        (
            [a] => 1
            [b] => 2
        )
)
```
This limitation has nothing to do with the way the code is implemented and has no solution.

## Performance

Usually these tests are done by developers to show off and have no other use :-)

Using $sampleArray from the example on an old Intel Intel Quad Core CPU (1000 interactions):
```php
flatten_array: 4.2939186096191 ms.
unflatten_array: 8.8309516906738 ms.
```

## Alternatives

A utility function to mainly flatten multidimensional-arrays and traversables into a one-dimensional array, preserving keys and joining them with a customizable separator to from fully-qualified keys in the final array:

https://github.com/AlaaSarhan/php-flatten

A php package to flatten nested json objects and nested arrays. It also allows you to create csv files from the flattened data:

https://github.com/tonirilix/nested-json-flattener

Recursively fold a multidimensional array into a unidimensional array, optionally preserving keys:

https://github.com/danielsdeboer/array-fold

## License

MIT License
