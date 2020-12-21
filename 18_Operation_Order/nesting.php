<?php

// Copyright 2020 Max Sprauer

//$line = '(6 + (2 * 3)) + (3 * (8 * 6) + 4 * 5 + 9) + (7 * 8 * 2 + (2 * 3 * 6 + 2) * 4) * 7 + 8 * 4';

assert(parse('1 + ( 2 * 3 ) + ( 4 * ( 5 + 6 ) )') == 51);
assert(parse('( ( 2 + 4 * 9 ) * ( 6 + 9 * 8 + 6 ) + 6 ) + 2 + 4 * 2') == 13632);

$acc = 0;
$lines = explode("\n", file_get_contents('input.txt'));

foreach ($lines as &$line) {
    $line = str_replace(['(', ')'], ['( ', ' )'], $line);
    $acc += parse($line);
}

print "Part One: $acc\n";


// For Part Two, I read a bit about parsing tree and operator precedence parsing.  I really hate
// RPN.  I had the idea of inserting extra parentheses and using my part one code, and then I read 
// here that it's actually done that way in Fortran (!!).
// https://en.wikipedia.org/wiki/Operator-precedence_parser

assert(parse(preparse('1 + (2 * 3) + (4 * (5 + 6))')) == 51);
assert(parse(preparse('2 * 3 + (4 * 5)')) == 46);
assert(parse(preparse('5 + (8 * 3 + 9 + 3 * 4 * 3)')) == 1445);
assert(parse(preparse('5 * 9 * (7 * 3 * 3 + 9 * 3 + (8 + 6 * 4))')) == 669060);
assert(parse(preparse('((2 + 4 * 9) * (6 + 9 * 8 + 6) + 6) + 2 + 4 * 2')) == 23340);

$acc = 0;
$lines = explode("\n", file_get_contents('input.txt'));

foreach ($lines as &$line) {
    $line = preparse($line);
    $acc += parse($line);
}

print "Part Two: $acc\n";   


function parse($expr = null)
{
    $acc = 0;
    $op = null;

    if ($expr) {
        $x = strtok($expr, ' ');
    } else {
        $x = strtok(' ');        
    }

    do {
        switch ($x) {
            case '(':
                // Start parsing sub expression
                if (is_null($op)) {
                    $acc = parse();
                } else {
                    $acc = eval("return $acc $op parse();");
                }
                break;

            case ')':
                // Stop parsing current expression
                return $acc;
                break;

            case '+':
            case '*':
                $op = $x;
                break;

            default:
                assert(is_numeric($x));

                if (is_null($op)) {
                    $acc = $x;
                } else {
                    $acc = eval("return $acc $op $x;");
                }

                break;
        }


        $x = strtok(' ');
    } while ($x !== false);

    return $acc;
}

function preparse($expr)
{
    print "$expr -> ";
    $expr = str_replace(['(', ')', '+', '*'], ['( ( ( (', ') ) ) )', ')) + ((', '))) * (((',], $expr);
    $expr = "(((($expr))))";
    $expr = str_replace(['(', ')'], ['( ', ' )'], $expr);
    $expr = preg_replace('/\s+/', ' ', $expr);
    print "$expr\n";
    return $expr;
}