<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Jfcherng\Utility\MbString;

/**
 * Do the benchmark on a string.
 *
 * @param string $str   the string to be tested
 * @param int    $runs  the wanted iteration counts
 * @param string $title the benchmark title
 */
function benchmark(string $str, int $runs, string $title = ''): void
{
    if ($title === '') {
        $title = 'Untitled';
    }

    $separator = \str_repeat('=', \strlen($title));
    echo "{$title}\n{$separator}\n";

    //////////////////
    // begin mb_*() //
    //////////////////

    $time = \microtime(true);

    $strLen = \mb_strlen($str, 'UTF-8');
    for ($run = 0; $run < $runs; ++$run) {
        for ($i = 0; $i < $strLen; ++$i) {
            // get nth char
            $char = \mb_substr($str, $i, 1, 'UTF-8');
            // in-place replacement of nth char
            $str = \mb_substr($str, 0, $i) . '停' . \mb_substr($str, $i + 1);
        }
    }

    $timeMbNative = \microtime(true) - $time;
    echo "PHP mb_*(): {$timeMbNative}\n";

    ////////////////////
    // begin MbString //
    ////////////////////

    $time = \microtime(true);

    $mbStr = new MbString($str, 'UTF-8');
    $strLen = $mbStr->strlen();
    for ($run = 0; $run < $runs; ++$run) {
        for ($i = 0; $i < $strLen; ++$i) {
            // get nth char
            $char = $mbStr[$i];
            // in-place replacement of nth char
            $mbStr->substr_replace_i('停', $i, 1);
        }
    }

    $timeMbString = \microtime(true) - $time;
    echo "MbString: {$timeMbString}\n";

    /////////
    // end //
    /////////

    echo "{$separator}\n";
    echo "Nums of Chars: {$strLen}\n";
    echo "Nums of Runs: {$runs}\n";

    $speedUp = $timeMbNative / $timeMbString - 1;
    $speedUpPercent = \round($speedUp * 100, 2);
    echo "Speed up: {$speedUpPercent}%\n\n";
}

$benchmarks = [
    'very-short.txt' => 1e4,
    'short.txt' => 1e3,
    'long.txt' => 1e2,
    'very-long.txt' => 1e1,
];

foreach ($benchmarks as $file => $runs) {
    benchmark(
        \file_get_contents(__DIR__ . "/{$file}"),
        (int) $runs,
        "# BENCHMARK: {$file}"
    );
}
