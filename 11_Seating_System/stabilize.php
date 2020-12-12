<?php

// Copyright 2020 Max Sprauer

$lines = explode("\n", file_get_contents('input.txt'));

do {
    $prev = $lines;
    $lines = cycle($lines, 4, 'occupiedAdjacent');

    for ($y = 0; $y < count($lines); $y++) {
            print $lines[$y] . "\n";
    }
    print "\n";
    usleep(100000);
} while ($prev != $lines);

$count = 0;
for ($y = 0; $y < count($lines); $y++) {
    for ($x = 0; $x < strlen($lines[0]); $x++) {
        if ($lines[$y][$x] == '#')
            $count++;
    }
}

print "Part One: $count\n";

/*
 * Part Two
 */

$lines = explode("\n", file_get_contents('input.txt'));

do {
    $prev = $lines;
    $lines = cycle($lines, 5, 'occupiedVisible');

    for ($y = 0; $y < count($lines); $y++) {
        print $lines[$y] . "\n";
    }
    print "\n";
    usleep(100000);
} while ($prev != $lines);

$count = 0;
for ($y = 0; $y < count($lines); $y++) {
    for ($x = 0; $x < strlen($lines[0]); $x++) {
        if ($lines[$y][$x] == '#')
            $count++;
    }
}

print "Part Two: $count\n";



function occupiedAdjacent($seats, $x, $y)
{
    $count = 0;

    for ($dy = $y - 1; $dy <= $y + 1; $dy++) {
        for ($dx = $x - 1; $dx <= $x + 1; $dx++) {
            if ($dx == $x && $dy == $y)
                continue;

            if ($dx < 0 || $dx >= strlen($seats[0]))
                continue;

            if ($dy < 0 || $dy >= count($seats))
                continue;

            if ($seats[$dy][$dx] == '#')
                $count++;
        }
    }

    return $count;
}

function occupiedVisible($seats, $ox, $oy)
{
    $count = 0;

    $dirs = array(
        [-1, -1], [-1, 0], [-1, 1],
        [0, -1],           [0, 1],
        [1, -1], [1, 0], [1, 1]
    );

    foreach ($dirs as $dir) {
        $done = false;
        $y = $oy;
        $x = $ox;

        do {
            $y += $dir[0];
            $x += $dir[1];

            if ($x < 0 || $x >= strlen($seats[0]))
                $done = true;

            if ($y < 0 || $y >= count($seats))
                $done = true;

            if (!$done) {
                switch ($seats[$y][$x]) {
                    case 'L':
                        $done = true;
                        break;
                    case '#':
                        $done = true;
                        $count++;
                        break;
                }
            }
        } while (!$done);
    }

    return $count;
}

function cycle($curr, $emptyThreshold, $occupiedFunc)
{
    $next = $curr;

    for ($y = 0; $y < count($curr); $y++) {
        for ($x = 0; $x < strlen($curr[$y]); $x++) {
            switch ($curr[$y][$x]) {
                case 'L':
                    // If a seat is empty (L) and there are no occupied seats adjacent to it, the seat becomes occupied.
                    if ($occupiedFunc($curr, $x, $y) == 0) {
                        $next[$y][$x] = '#';
                    }
                    break;
                case '#':
                    // If a seat is occupied (#) and four or more seats adjacent to it are also occupied, the seat becomes empty.
                    if ($occupiedFunc($curr, $x, $y) >= $emptyThreshold) {
                        $next[$y][$x] = 'L';
                    }                                        
                    break;
            }
        }
    }

    return $next;
}