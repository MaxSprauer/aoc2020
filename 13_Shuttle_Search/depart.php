<?php

// Copyright 2020 Max Sprauer

$lines = explode("\n", file_get_contents('input.txt'));

$depart = explode(',', $lines[1]);

// Sort largest -> smallest intervals
$sorted = array_filter($depart, function($var) { return $var != 'x'; });
rsort($sorted);

print "Departures:\n";
print_r($depart);

print "Sorted:\n";
print_r($sorted);

$longestCycle = $sorted[0];


/* After running for several hours: there's a pattern here -- these timestamps of 4-route 
   matches are 15255760561 apart.  Was there a smarter way to figure this out?  Not sure.
   I thought about Least Common Multiple, but the offsets seemed to prevent that.  Maybe
   solving simultaneous equations?
[100011164648668] Cycle: 29 Offset: 0
[100026420409229] Cycle: 29 Offset: 0
[100041676169790] Cycle: 29 Offset: 0
[100056931930351] Cycle: 29 Offset: 0
[100072187690912] Cycle: 29 Offset: 0
[100087443451473] Cycle: 29 Offset: 0
[100102699212034] Cycle: 29 Offset: 0
[100117954972595] Cycle: 29 Offset: 0
[100133210733156] Cycle: 29 Offset: 0
[100148466493717] Cycle: 29 Offset: 0
[100163722254278] Cycle: 29 Offset: 0
*/

// $lcTimestamp is the time the bus with the longest cycle arrived
for ($lcTimestamp = 100011164648668; ; $lcTimestamp += 15255760561) {
    $lcOffset = array_search($longestCycle, $depart);

    // Loop thru other schedules in largest -> smallest order
    for ($jj = 1; $jj < count($sorted); $jj++) {
        $curCycle = $sorted[$jj];
        $curOffset = array_search($curCycle, $depart); 

        if (($lcTimestamp - $lcOffset + $curOffset) % $curCycle == 0) {
            if ($jj > 3) {
                print "[$lcTimestamp] Cycle: $curCycle Offset: $curOffset\n";
            }

            if ($jj == (count($sorted) - 1)) {
                print "Part Two: " . ($lcTimestamp - $lcOffset) . "\n";
                exit;
            }
        } else {
            continue 2;
        }
    }
}
