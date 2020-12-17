<?php

// Copyright 2020 Max Sprauer

class Range {
    public $min;
    public $max;

    function __construct($min, $max)
    {
        $this->min = (int) $min;
        $this->max = (int) $max;
    }

    function inside($x)
    {
        return (((int) $x >= $this->min) && ((int) $x <= $this->max));
    }
}

$lines = explode("\n", file_get_contents('fields.txt'));
$ranges = array();

foreach ($lines as $line) {
    // arrival location: 42-636 or 642-962
    if (preg_match('/^.*: (\d+)-(\d+) or (\d+)-(\d+)$/', $line, $m)) {
        $ranges[] = new Range($m[1], $m[2]);
        $ranges[] = new Range($m[3], $m[4]);
    } else {
        assert(false, $line);
    }
}

$lines = explode("\n", file_get_contents('nearby.txt'));
$errorRate = 0;

foreach ($lines as $line) {
    $nums = explode(',', $line);
    foreach ($nums as $num) {
        $inOne = false;
        foreach ($ranges as $range) {
            if ($range->inside($num)) {
                $inOne = true;
                break;
            }
        }

        if (!$inOne) {
            $errorRate += $num;
        }
    }
}

print "Part One: $errorRate\n";
