<?php

// Copyright 2020 Max Sprauer

$lines = explode("\n", file_get_contents('input.txt'));

// Build $active array
$active = array();  // z, y, x
foreach ($lines as $y => $line) {
    for ($x = 0; $x < strlen($line); $x++) {
        if ($line[$x] == '#') {
            $active[0][$y][$x] = '#';
        }
    }
}

printMap(0, $active);

// Life cycle 6 times
for ($cycle = 0; $cycle < 6; $cycle++) {
    $newState = array();

    foreach ($active as $origz => $yarr) {
        foreach ($yarr as $origy => $xarr) {
            foreach ($xarr as $origx => $val) {
                for ($z = $origz - 1; $z <= $origz + 1; $z++) {
                    for ($y = $origy - 1; $y <= $origy + 1; $y++) {
                        for ($x = $origx - 1; $x <= $origx + 1; $x++) {            
                            $c = countActiveNeighbors($active, $x, $y, $z);
                            if (isset($active[$z][$y][$x])) {
                                if ($c == 2 || $c == 3) {
                                    $newState[$z][$y][$x] = '#';
                                }
                            } else {
                                if ($c == 3) {
                                    $newState[$z][$y][$x] = '#';
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

print "Part One: $activeCount\n";

function countActiveNeighbors(&$active, $origx, $origy, $origz)
{
    $count = 0;

    for ($z = $origz - 1; $z <= $origz + 1; $z++) {
        for ($y = $origy - 1; $y <= $origy + 1; $y++) {
            for ($x = $origx - 1; $x <= $origx + 1; $x++) {
                if (($x == $origx) && ($y == $origy) && ($z == $origz)) {
                    continue;
                }

                if (isset($active[$z][$y][$x])) {
                    $count++;
                }
            }
        }
    }

    return $count;
}

function printMap($cycle, &$active)
{
    print "After cycle $cycle:\n";

    for ($z = -10; $z <= 10; $z++) {
        if (isset($active[$z])) {
            print "z = $z\n";

            for ($y = -10; $y <= 10; $y++) {
                for ($x = -10; $x <= 10; $x++) {
                    print (isset($active[$z][$y][$x])) ? '#' : '.';
                }
                print "\n";
            }
        }
    }
}