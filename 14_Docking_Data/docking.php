<?php

// Copyright 2020 Max Sprauer

$lines = explode("\n", file_get_contents('input.txt'));


/* 
    mask = 000001001011XX1XX100X0001011X0001101
    mem[54977] = 194579
*/

$mem = array();

foreach ($lines as $line) {
    if (preg_match('/^mask = (.*)$/', $line, $m)) {
        $mask = $m[1];

        $orMask = 0;
        $andMask = pow(2, 37) - 1;  // 36 bits set

        for ($ii = strlen($mask); $ii >= 0; $ii--) {
            $maskIndex = strlen($mask) - $ii;
            
            if ($mask[$ii] == '0') {
                $andMask &= ~(1 << ($maskIndex - 1));
            } else if ($mask[$ii] == '1') {
                $orMask |= (1 << ($maskIndex - 1));
            }
        }

        // print "$mask andMask = " . decbin($andMask) . ", orMask = " . decbin($orMask) . "\n";

    } else if (preg_match('/^mem\[(\d+)\] = (\d+)$/', $line, $m)) {
        $loc = (int) $m[1];
        $val = (int) $m[2];

        $val &= $andMask;
        $val |= $orMask;

        $mem[$loc] = $val;
    } else {
        assert(false, $line);
    }
}

print_r($mem);
print "Part One: " . array_sum($mem) . "\n";