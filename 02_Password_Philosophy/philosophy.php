<?php

// Copyright 2020 Max Sprauer

$lines = explode("\n", file_get_contents('input.txt'));
$valid = 0;
$validTwo = 0;

foreach ($lines as $line) {
    // 4-5 p: pcpkvp
    if (preg_match('/(\d+)-(\d+) ([a-z]): ([a-z]+)/', $line, $m)) {
        if (validPassword($m[3], $m[1], $m[2], $m[4])) {
            $valid++;
        }

        if (validPasswordPartTwo($m[3], $m[1], $m[2], $m[4])) {
            $validTwo++;
        }
    } else {
        print "Bad line: $line\n";
        exit;
    }
}

print "Part One Valid: $valid\n";
print "Part Two Valid: $validTwo\n";

function validPassword($letter, $min, $max, $string)
{
    // I am going to programmer hell for this
    $a = count_chars($string, 1);
    return ($a[ord($letter)] >= $min) && ($a[ord($letter)] <= $max);
}

function validPasswordPartTwo($letter, $pos1, $pos2, $string)
{
    $atP1 = ($string[$pos1 - 1] === $letter);
    $atP2 = ($string[$pos2 - 1] === $letter);

    return ($atP1 xor $atP2);
}