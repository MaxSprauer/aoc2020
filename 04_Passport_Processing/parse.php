<?php

// Copyright 2020 Max Sprauer

$req = array_flip(array('byr', 'iyr', 'eyr', 'hgt', 'hcl', 'ecl', 'pid'));
$eyeColors = array('amb', 'blu', 'brn', 'gry', 'grn', 'hzl', 'oth');

$txt = file_get_contents('input.txt');

$groups = preg_split("/\n\n/", $txt);

$valid = 0;
$validTwo = 0;
foreach ($groups as $group) {
    $fields = preg_split('/\s+/', $group);
    $passport = array();
    foreach ($fields as $field) {
        list($name, $value) = explode(':', $field);
        $passport[$name] = $value;
    }

    if (count(array_intersect_key($req, $passport)) == count($req)) {
        $valid++;

        $partTwoValid = true;
        foreach ($passport as $name => $value) {
            switch ($name) {
                case 'byr':
                    $partTwoValid = $partTwoValid && validNumber($value, 4, 1920, 2002);
                break;

                case 'iyr':
                    $partTwoValid = $partTwoValid && validNumber($value, 4, 2010, 2020);
                break;

                case 'eyr':
                    $partTwoValid = $partTwoValid && validNumber($value, 4, 2020, 2030);
                break;

                case 'hgt':
                    if (preg_match('/^(\d+)(in|cm)$/', trim($value), $m)) {
                        $partTwoValid = $partTwoValid &&
                            (
                                ($m[2] == 'in' && validNumber($m[1], 2, 59, 76))
                                || ($m[2] == 'cm' && validNumber($m[1], 3, 150, 193))
                            );
                    } else {
                        $partTwoValid = false;
                    }
                break;

                case 'hcl':
                    $partTwoValid = $partTwoValid && preg_match('/^#[0-9a-f]{6}$/', trim($value));
                break;

                case 'ecl':
                    $partTwoValid = $partTwoValid && in_array($value, $eyeColors);
                break;

                case 'pid':
                    $partTwoValid = $partTwoValid && validNumber($value, 9);
                break;
            }
        }

        if ($partTwoValid) {
            $validTwo++;
        }
    }
}

print "Part One: $valid\n";
print "Part Two: $validTwo\n";

function validNumber($num, $digits, $min = null, $max = null)
{
    if (is_numeric($num) && strlen($num) == $digits) {
        if ((!$min || $num >= $min) && (!$max || $num <= $max)) {
            return true;
        }
    }

    return false;
}
