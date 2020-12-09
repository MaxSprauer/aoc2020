<?php

// Copyright 2020 Max Sprauer
define('WINDOW', 25);

$lines = explode("\n", file_get_contents('input.txt'));

for ($ii = 25; $ii < count($lines); $ii++) {
    if (!isSumOfPrevious($ii)) {
        print "Part One: {$lines[$ii]} index $ii is not a sum of two previous.\n";
        break;
    }
}

function isSumOfPrevious($index)
{
    global $lines;
    $val = (int) $lines[$index];

    for ($ii = $index - WINDOW; $ii < $index; $ii++) {
        for ($jj = $ii + 1; $jj < $index; $jj++) {
            if ($lines[$ii] + $lines[$jj] === $val) {
                return true;
            }
        }
    }

    return false;
}

define('TARGET', 50047984);
function findSumOfRange()
{
    global $lines;

    for ($start = 0; $start < count($lines); $start++) {

        // Try each range until we hit it or go over
        $sum = $lines[$start];
        $end = $start + 1;
        $rangeMin = $lines[$start];
        $rangeMax = $lines[$start];

        while ($end < count($lines) && $sum < TARGET) {
            $sum += $lines[$end];
            $rangeMin = min($rangeMin, $lines[$end]);
            $rangeMax = max($rangeMax, $lines[$end]);

            $end++;
        }

        if ($sum == TARGET) {
            $answer = $rangeMin + $rangeMax;
            $end--;
            print "Part Two: Start index: $start, End index: $end, answer: $answer\n";
            return;
        }
    }

    print "Part Two: Not found\n";
}

findSumOfRange();