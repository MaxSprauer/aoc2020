<?php

// Copyright 2020 Max Sprauer

$lines = explode("\n", file_get_contents('input.txt'));

$children = array();

class Child {
    public $color;
    public $count;

    function __construct($color, $count)
    {
        $this->color = $color;
        $this->count = $count;
    }
}

foreach ($lines as $line) {

    /* posh cyan bags contain 2 vibrant gray bags.
       dim lime bags contain 5 muted tomato bags, 5 posh gray bags, 4 pale green bags, 4 striped silver bags.
       mirrored lavender bags contain no other bags.
   */

    assert(preg_match('/^(.*) bags contain/', $line, $m));
    $parent = $m[1];
    assert(!isset($children[$parent]));
    $children[$parent] = array();

    if (!preg_match('/.*no other bags\.$/', $line)) {
        // TIL: Each capture group will only ever match once, not matter how badly you want them to match all.
        // That's why php has preg_match_all().
        assert(preg_match_all('/(\d+) ([^,\.]+) bag[s]?[,\.]/', $line, $m));

        for ($ii = 0; $ii < count($m[1]); $ii++) {
            $children[$parent][] = new Child($m[2][$ii], $m[1][$ii]);
        }
    }

}

// List of all ancestors on shiny gold
$ancestors = array();

// Trace each ancestor back up the stack
findAncestors('shiny gold');

function findAncestors($target)
{
    global $ancestors, $children;

    foreach ($children as $color => $childArray) {
        foreach ($childArray as $child) {
            if ($child->color == $target) {
                if (!in_array($color, $ancestors)) {
                    $ancestors[] = $color;
                    findAncestors($color);
                }
            }
        }
    }
}

print "Part One:\n";
print_r($ancestors);


function sumDescendants($target, $multiplier)
{
    global $children;
    $sum = 0;

    foreach ($children[$target] as $child) {
        $sum += $multiplier * $child->count;
        $sum += sumDescendants($child->color, $multiplier * $child->count);
    }

    return $sum;
}

$total = sumDescendants('shiny gold', 1);

print "Part Two: $total\n";
