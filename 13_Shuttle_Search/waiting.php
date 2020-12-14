<?php

// Copyright 2020 Max Sprauer

$lines = explode("\n", file_get_contents('input.txt'));

$target = $lines[0];
$times = explode(',', $lines[1]);
$times = array_filter($times, function($var) { return $var != 'x'; });
//$times = array_flip($times);

$firstAfterTime = -1;
$firstAfterRoute = -1;

foreach ($times as $time ) {
    // Find the first time after the target that each bus arrives
    $t = (intdiv($target, $time) + 1) * $time;

    if ($firstAfterTime == -1 || $t < $firstAfterTime) {
        $firstAfterTime = $t;
        $firstAfterRoute = $time;
    }
}

$answer = $firstAfterRoute * ($firstAfterTime - $target);
print "Part One: $answer\n";