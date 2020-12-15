<?php

// Copyright 2020 Max Sprauer

$spoken = array(1 => 8, 2 => 0, 3 => 17, 4 => 4, 5 => 1, 6 => 12);
$spokenCounts = array(8 => 1, 0 => 1, 17 => 1, 4 => 1, 1 => 1, 12 => 1);

for ($turn = count($spoken) + 1; $turn <= 2020; $turn++) {
    $previouslySpoken = $spoken[$turn - 1];
    
    if ($spokenCounts[$previouslySpoken] == 1) {
        // The previous number was only spoken once.  Speak 0.
        $speak = 0;
    } else {
        // Speak the number of turns since previously spoken
        for ($ii = $turn - 2; $ii >= 0; $ii--) {
            if ($spoken[$ii] == $previouslySpoken) {
                break;
            }
        }

        $speak = $turn - 1 - $ii;
    }


    printf("%4d: %4d\n", $turn, $speak);
    if ($turn % 1000 == 0) {
        print_r($spokenCounts);
    }


    $spoken[] = $speak;
    if (isset($spokenCounts[$speak])) {
        $spokenCounts[$speak]++;
    } else {
        $spokenCounts[$speak] = 1;
    }
}

print "Part One: $speak\n";


// For Part Two, optimize by only keeping track of the most recent time each number was spoken
// to prevent having to iterate back over the list of spoken numbers.  We also don't really
// need the $spoken array of every number spoken, just the previous.
ini_set('memory_limit', '1G');

$spoken = array(1 => 8, 2 => 0, 3 => 17, 4 => 4, 5 => 1, 6 => 12);  // Turn => number
$lastSpoken = array_flip($spoken);  // Number => turn

for ($turn = count($spoken) + 1; $turn <= 30000000; $turn++) {
    $previouslySpoken = $spoken[$turn - 1];
    
    if (!isset($lastSpoken[$previouslySpoken])) {
        // The previous number was only spoken once.  Speak 0.
        $speak = 0;        
    } else {
        // Speak the number of turns since previously spoken
        $speak = $turn - 1 - $lastSpoken[$previouslySpoken];
    }

    if ($turn % 1000 == 0) {
        printf("%10d: %10d\n", $turn, $speak);
    }

    $spoken[] = $speak;
    $spoken = array_slice($spoken, -3, null, true);     // Not super efficient, but saves memory.  A rotating buffer would be better.
    $lastSpoken[$previouslySpoken] = $turn - 1;
}

print "Part Two: $speak\n";