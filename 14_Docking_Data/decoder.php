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
    } else if (preg_match('/^mem\[(\d+)\] = (\d+)$/', $line, $m)) {
        $loc = (int) $m[1];
        $val = (int) $m[2];
        $floatInds = array();

        for ($ii = strlen($mask) - 1; $ii >= 0; $ii--) {
            $maskIndex = strlen($mask) - $ii;

            if ($mask[$ii] == '1') {
                $loc |= (1 << ($maskIndex - 1));
            } else if ($mask[$ii] == 'X') {
                $floatInds[] = $maskIndex - 1;
            }
        }

        print_r($floatInds);

        $maskedloc = gmp_init($loc);
        for ($ii = 0; $ii < pow(2, count($floatInds)); $ii++) {
            foreach ($floatInds as $pos => $floatInd) {
                // $ii is the sequence of the numbers to be used to populate the X's
                // $floatInds is the sequence of slots to fill        
                gmp_setbit($maskedloc, $floatInd, gmp_testbit($ii, $pos));
            }
            print "Writing addr " . gmp_intval($maskedloc) . " = $val\n";
            $mem[gmp_intval($maskedloc)] = $val;
        }
    } else {
        assert(false, $line);
    }
}

print_r($mem);
print "Part Two: " . array_sum($mem) . "\n";