<?php

// Copyright 2020 Max Sprauer

$lines = explode("\n", file_get_contents('input.txt'));
$count = count($lines);

for ($ii = 0; $ii < $count; $ii++) {
    for ($jj = $ii + 1; $jj < $count; $jj++) {
        if ($lines[$ii] + $lines[$jj] == 2020) {
            print 'Part One: ' . ($lines[$ii] * $lines[$jj]) . "\n";
            break 2;
        }
    }
}

for ($ii = 0; $ii < $count; $ii++) {
    for ($jj = $ii + 1; $jj < $count; $jj++) {
        for ($kk = $jj + 1; $kk < $count; $kk++) {
            if ($lines[$ii] + $lines[$jj] + $lines[$kk] == 2020) {
                print 'Part Two: ' . ($lines[$ii] * $lines[$jj] * $lines[$kk]) . "\n";
                break 2;
            }
        }
    }
}
