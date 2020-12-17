<?php

// Copyright 2020 Max Sprauer

$lines = explode("\n", file_get_contents('input.txt'));

// Build $active array
$active = array();  // w, z, y, x
foreach ($lines as $y => $line) {
    for ($x = 0; $x < strlen($line); $x++) {
        if ($line[$x] == '#') {
            $active[0][0][$y][$x] = '#';
        }
    }
}

printMap(0, $active);

// Life cycle 6 times
for ($cycle = 0; $cycle < 6; $cycle++) {
    $newState = array();

    foreach ($active as $origw => $zarr) {
        foreach ($zarr as $origz => $yarr) {
            foreach ($yarr as $origy => $xarr) {
                foreach ($xarr as $origx => $val) {
                    for ($w = $origw - 1; $w <= $origw + 1; $w++) {
                        for ($z = $origz - 1; $z <= $origz + 1; $z++) {
                            for ($y = $origy - 1; $y <= $origy + 1; $y++) {
                                for ($x = $origx - 1; $x <= $origx + 1; $x++) {            
                                    $c = countActiveNeighbors($active, $x, $y, $z, $w);
                                    if (isset($active[$w][$z][$y][$x])) {
                                        if ($c == 2 || $c == 3) {
                                            $newState[$w][$z][$y][$x] = '#';
                                        }
                                    } else {
                                        if ($c == 3) {
                                            $newState[$w][$z][$y][$x] = '#';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $active = $newState;
    $activeCount = 0;
    assert(array_walk_recursive($active, function ($val, $key) use (&$activeCount) { $activeCount++; }));
    print "$activeCount\n";
    printMap($cycle + 1, $active);
}

print "Part Two: $activeCount\n";

function countActiveNeighbors(&$active, $origx, $origy, $origz, $origw)
{
    $count = 0;

    for ($w = $origw - 1; $w <= $origw + 1; $w++) {
        for ($z = $origz - 1; $z <= $origz + 1; $z++) {
            for ($y = $origy - 1; $y <= $origy + 1; $y++) {
                for ($x = $origx - 1; $x <= $origx + 1; $x++) {
                    if (($x == $origx) && ($y == $origy) && ($z == $origz) && ($w == $origw)) {
                        continue;
                    }

                    if (isset($active[$w][$z][$y][$x])) {
                        $count++;
                    }
                }
            }
        }
    }

    return $count;
}

function printMap($cycle, &$active)
{
    print "After cycle $cycle:\n";

    for ($w = -10; $w <= 10; $w++) {
        for ($z = -10; $z <= 10; $z++) {
            if (isset($active[$w][$z])) {
                print "z = $z, w = $w\n";

                for ($y = -10; $y <= 10; $y++) {
                    for ($x = -10; $x <= 10; $x++) {
                        print (isset($active[$w][$z][$y][$x])) ? '#' : '.';
                    }
                    print "\n";
                }
            }
        }
    }
}