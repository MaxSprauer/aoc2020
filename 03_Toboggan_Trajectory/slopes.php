<?php

// Copyright 2020 Max Sprauer

// 0,0 is top left
$map = explode("\n", file_get_contents('input.txt'));

$mapHeight = count($map);
$mapWidth = strlen($map[0]);

$treeCount = findTreeCount(3, 1);
print "Part One: $treeCount\n";

$treeCount *= findTreeCount(1, 1);
$treeCount *= findTreeCount(5, 1);
$treeCount *= findTreeCount(7, 1);
$treeCount *= findTreeCount(1, 2);
print "Part Two: $treeCount\n";

function findTreeCount($dx, $dy)
{
    global $map, $mapWidth, $mapHeight;
    $treeCount = 0;
    
    for ($posX = 0, $posY = 0; $posY <= $mapHeight; $posX += $dx, $posY += $dy) {
        if ($map[$posY][$posX % $mapWidth] == '#') {
            print "Tree at $posX, $posY\n";
            $treeCount++;
        }
    }
    
    return $treeCount;
}