<?php

include __DIR__ . '/vendor/autoload.php';

use Jfcherng\Utility\LevenshteinDistance;

$results = LevenshteinDistance::calculate(
    // old string
    '自訂取代詞語模組',
    // new string
    '自订取代词语模组！',
    // progress type
    LevenshteinDistance::PROGRESS_FULL
);

// [
//     'distance' => 5,
//     'progresses' => [
//         ['ins', 7],
//         ['rep', 7, 7],
//         ['cpy', 6, 6],
//         ['rep', 5, 5],
//         ['rep', 4, 4],
//         ['cpy', 3, 3],
//         ['cpy', 2, 2],
//         ['rep', 1, 1],
//         ['cpy', 0, 0],
//     ],
// ]
var_dump($results);
