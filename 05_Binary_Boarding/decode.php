<?php

// Copyright 2020 Max Sprauer

$lines = explode("\n", file_get_contents('input.txt'));

/*
$lines = [
    'BFFFBBFRRR',
    'FFFBBBFRRR',
    'BBFFBBFRLL'
];
*/

$maxSeatID = 0;
$occupied = array();

foreach ($lines as $line) {
    $rowMin = 0;
    $rowMax = 127;
    $colMin = 0;
    $colMax = 7;

    for ($ii = 0; $ii < strlen($line); $ii++) {
        switch ($line[$ii]) {
            case 'F':
                bsp($rowMax, $rowMin, false);
            break;

            case 'B':
                bsp($rowMax, $rowMin, true);
            break;

            case 'R':
                bsp($colMax, $colMin, true);
            break;

            case 'L':
                bsp($colMax, $colMin, false);
            break;

            default:
                assert(false, "Bad char: " . $line[$ii]);
            break;
        }        
    }

    assert($colMin == $colMax);
    assert($rowMin == $rowMax);

    $seatID = ($rowMin * 8) + $colMin;
    $occupied[$seatID] = 1;
    $maxSeatID = max($maxSeatID, $seatID);
}

print "Part One: $maxSeatID\n"; // 917 too high

for ($ii = $maxSeatID; $ii >= 0; $ii--) {
    if (!isset($occupied[$ii])) {
        print "Part Two: $ii\n";
        break;
    }
}

function bsp(&$max, &$min, $takeUpper)
{
    $half = ($max - $min + 1) / 2;

    if ($takeUpper) {
        $min = $max - $half + 1;
    } else {
        $max = $min + $half - 1;
    }
}
