<?php

// Copyright 2020 Max Sprauer

$lines = explode("\n", file_get_contents('input.txt'));
sort($lines, SORT_NUMERIC);

$skipOne = 1;       // The charging outlet has an effective rating of 0 jolts, and the first number is 1  
$skipThree = 1;     // your device's built-in adapter is always 3 higher than the highest adapter

for ($ii = 1; $ii < count($lines); $ii++) {
    $diff = $lines[$ii] - $lines[$ii - 1];
    if ($diff == 1) {
        $skipOne++;
    } else if ($diff == 3) {
        $skipThree++;
    }
}

$answer = $skipOne * $skipThree;
print "Part One: $answer\n";


// Part Two: Cache and sum counts of subtrees.  "Counts" are the number of paths
// from the current number to the end number.  Each time you reach a number, the
// number of paths to the end from that number will always be the same, no matter
// where in the tree you are.  By doing a DFS and saving counts for
// each number, subsequent trips down the tree are much faster.

// I tried many things, thinking that the answer would be based on permutations and 
// multiplication, but nope, just good ol' addition.
$lines = explode("\n", file_get_contents('input.txt'));
sort($lines, SORT_NUMERIC);
array_unshift($lines, 0);
$subtreeCounts = array();
print "Part Two: " . dfs2(0, '') . "\n";

// Returns the total number of subtree paths
function dfs2($index, $pathSoFar)
{
    global $lines, $subtreeCounts;
    $val = $lines[$index];
    $lastIndex = count($lines) - 1;
    $pathCount = 0;     // Count of paths from current number to end number

    // The list is sorted.  Check the three following values to see if they are within 1-3.
    for ($ii = $index + 1; $ii <= $index + 3 && $ii <= $lastIndex; $ii++) {
        if ($lines[$ii] - $val <= 3) {
            if ($ii < $lastIndex) {
                // We have a valid next number, but need to recurse further.

                // Have we already calculated this subtree's path count?
                if (!isset($subtreeCounts[$lines[$ii]])) {
                    $subtreeCounts[$lines[$ii]] = dfs2($ii, "$pathSoFar $val");
                }

                $pathCount += $subtreeCounts[$lines[$ii]];
            } else if ($ii == $lastIndex) {
                // We hit the end
                print "$pathSoFar $val {$lines[$ii]}\n";
                $pathCount += 1;            
            }  
        } 
    }

    return $pathCount;
}

exit;

/*
 * Failed attempts below
 */

// Part Two: An attempt to do some math based on number of nodes that can reach current
// node and number that can be reached from current node.
$lines = explode("\n", file_get_contents('input2.txt'));
sort($lines, SORT_NUMERIC);
array_unshift($lines, 0);
$arrangements = 1;
$lastIndex = count($lines) - 1;

for ($ii = 0; $ii <= $lastIndex; $ii++) {
    $pathsToMe = 0;
    for ($jj = $ii - 3; $jj < $ii; $jj++) {
        if ($jj >= 0) {
            if ($lines[$ii] - $lines[$jj] <= 3) {
                $pathsToMe++;
            }
        }
    }

    $pathsFromMe = 0;
    for ($jj = $ii + 1; $jj <= $ii + 3; $jj++) {
        if ($jj <= $lastIndex) {
            if ($lines[$jj] - $lines[$ii] <= 3) {
                $pathsFromMe++;
            }
        }
    }

    $pathsToMe = max($pathsToMe, 1);
    $pathsFromMe = max($pathsFromMe, 1);
    $diff = abs($pathsFromMe - $pathsToMe);
    printf("%d: To me: %d  From me: %d  Diff:  %d\n", $lines[$ii], $pathsToMe, $pathsFromMe, abs($pathsFromMe - $pathsToMe));
    // $arrangements += abs($pathsFromMe - $pathsToMe);
    // $arrangements *= (pow(2, abs($pathsFromMe - $pathsToMe)));

// How many ways can the following 3 be arranged?
/*
1 2 3    1 2    1 3    3
1 2      1      1
1   3      2      3
1
  2 3
  2
    3





    1
    1
    1
    1
    1                   start with 1 9
    3            3      created 2 more 9's
   4  5  6     2 1   1  created 1 more 9
  5 6 6  9     1 1   1
  6 9 9        1
  9
*/


    // $arrangements += ($pathsFromMe * ($lastIndex - $ii + 1));
    // $arrangements = ($arrangements * $pathsFromMe / $pathsToMe);
    // $arrangements *= (pow(2, $pathsFromMe) - 1);

    $arrangements += ($pathsFromMe - 1 + $pathsToMe - 1);
}

print "Part Two: $arrangements\n";
exit;


// Part Two: When a node has more than one valid child, the number of ways to arrange the following
// children is 2 ^ (n-1).
$lines = explode("\n", file_get_contents('input.txt'));
sort($lines, SORT_NUMERIC);
array_unshift($lines, 0);
$arrangements = 1;
$lastIndex = count($lines) - 1;


for ($ii = 0; $ii < $lastIndex; $ii += $numValidChildren) {
    $numValidChildren = 0;

    for ($jj = $ii + 1; $jj <= $ii + 3 && $jj <= $lastIndex; $jj++) {
        if ($lines[$jj] - $lines[$ii] <= 3) {
            $numValidChildren++;
        }
    }

    $arrangements *= pow(2, ($numValidChildren - 1));
    print "{$lines[$ii]} -> num children: $numValidChildren\n";
}

print "Part Two: $arrangements\n";  // 68719476736 is too low

exit;

PART2:
// Part Two: Build a tree of possible sequences.  Count of leaf nodes is count of distinct ways.
// Probably do no need to save the entire tree as we build it...
// This works (I think), but it's too slow for the real input.
$lines = explode("\n", file_get_contents('inputx.txt'));
sort($lines, SORT_NUMERIC);
array_unshift($lines, 0);
$arrangements = 0;
dfs(0, '');
print "Part Two: $arrangements\n";

function dfs($index, $pathSoFar)
{
    global $lines, $arrangements;
    $val = $lines[$index];
    $lastIndex = count($lines) - 1;

    // The list is sorted.  Check the three following values to see if they are within 1-3.
    for ($ii = $index + 1; $ii <= $index + 3 && $ii <= $lastIndex; $ii++) {
        if ($lines[$ii] - $val <= 3) {
            if ($ii < $lastIndex) {
                dfs($ii, "$pathSoFar $val");
            } else if ($ii == $lastIndex) {
                $arrangements++;
                print "$pathSoFar $val {$lines[$ii]}\n";
                return;            
            }  
        } 
    }
}
