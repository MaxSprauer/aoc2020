<?php

// Copyright 2020 Max Sprauer

$groups = explode("\n\n", file_get_contents('input.txt'));

$unique = 0;
foreach ($groups as $group)
{
    $c = count_chars($group, 1);
    unset($c[10]);
    $unique += count($c);
}

print "Part One: $unique\n";

$total = 0;

foreach ($groups as $group)
{
    $people = explode("\n", $group);
    
    $answers = array(); // Count of yesses for each question
    foreach ($people as $person) {
        for ($ii = 0; $ii < strlen($person); $ii++) {
            $answers[$person[$ii]]++;
        }
    }

    foreach ($answers as $q => $c) {
        if ($c == count($people)) {
            $total++;
        }
    }
}

print "Part Two: $total\n";
