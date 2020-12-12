<?php

// Copyright 2020 Max Sprauer

class Ship
{
    public $x = 0;
    public $y = 0;
    public $waydx = 10;
    public $waydy = 1;
}

function mod($x, $n)
{
    $r = $x % $n;
    if ($r < 0)
    {
        $r += abs($n);
    }
    return $r;
}

$s = new Ship();
$lines = explode("\n", file_get_contents('input.txt'));

foreach ($lines as $line) {
    assert(2 === sscanf($line, '%1s%d', $dir, $dist));

    switch ($dir) {
        case 'N':
            $s->waydy += $dist;
            break;

        case 'S':
            $s->waydy -= $dist;
            break;

        case 'E':
            $s->waydx += $dist;
            break;

        case 'W':
            $s->waydx -= $dist;
            break;

        // I know there are general purpose coordinate rotation algorithms,
        // but this seems like a simple case.
        case 'L':
            // Flip x and y; negate new x
            $steps = ($dist / 90);
            print "$dir $dist {$s->waydx}, {$s->waydy} -> ";

            for ($i = 0; $i < $steps; $i++) {
                $t = $s->waydx;
                $s->waydx = -1 * $s->waydy;
                $s->waydy = $t;
            }
            print "{$s->waydx}, {$s->waydy}\n";

            break;

        case 'R':
            // Flip x and y; negate new y
            $steps = ($dist / 90);
            print "$dir $dist {$s->waydx}, {$s->waydy} -> ";

            for ($i = 0; $i < $steps; $i++) {
                $t = $s->waydy;
                $s->waydy = -1 * $s->waydx;
                $s->waydx = $t;
            }
            print "{$s->waydx}, {$s->waydy}\n";

            break;

        case 'F':
            $s->x += ($s->waydx * $dist);
            $s->y += ($s->waydy * $dist);
            break;

        default:
            assert(false, $line);
            break;
    }
}

print "Part Two: " . (abs($s->x) + abs($s->y)) . "\n";
