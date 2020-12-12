<?php

// Copyright 2020 Max Sprauer

class Ship
{
    public $facing = 'E';
    public $x = 0;
    public $y = 0;
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

$Rdir = ['N', 'E', 'S', 'W'];

foreach ($lines as $line) {
    assert(2 === sscanf($line, '%1s%d', $dir, $dist));
    $mult = 1;

    switch ($dir) {
        case 'N':
            $s->y -= $dist;
            break;

        case 'S':
            $s->y += $dist;
            break;

        case 'E':
            $s->x += $dist;
            break;

        case 'W':
            $s->x -= $dist;
            break;

        case 'L':
            $mult = -1;   
            // fallthru
        case 'R':
            print "Facing: {$s->facing}, $dir, $dist: ";
            // No style points, right?
            $curInd = array_search($s->facing, $Rdir);
            $steps = ($dist / 90) * $mult;
            $newInd = mod(($curInd + $steps), 4);
            $s->facing = $Rdir[$newInd];
            print "{$s->facing}\n";
            break;

        case 'F':
            switch ($s->facing) {
                case 'N':
                    $s->y -= $dist;
                    break;
        
                case 'S':
                    $s->y += $dist;
                    break;
        
                case 'E':
                    $s->x += $dist;
                    break;
        
                case 'W':
                    $s->x -= $dist;
                    break;
            }
            break;

        default:
            assert(false, $line);
            break;
    }
}

print "Part One: " . (abs($s->x) + abs($s->y)) . "\n";
