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

    function contains($x)
    {
        return (((int) $x >= $this->min) && ((int) $x <= $this->max));
    }
}

class Ticket {
    public $nums;
    public $matches;
    public $nonmatches;
    public $valid;

    function __construct($nums)
    {
        $this->nums = $nums;
        $this->matches = array();
        $this->nonmatches = array();
        $this->valid = true;
    }

    function __toString()
    {
        return implode(',', $this->nums);
    }

    function addMatch($num, $label)
    {
        $this->matches[$label][] = $num;
    }

    function addNonmatch($num, $label)
    {
        $this->nonmatches[$label][] = $num;
    }

    function printMatches()
    {
        foreach ($this->matches as $label => $nm) {
            print "$label [" . count($nm) . "]: " . implode(', ', $nm) . "\n";

            if (count($nm) < 20) {
                print "\tDidn't match: " . implode($this->nonmatches[$label]) . "\n";
            }
        }
    }

    function printNonmatches()
    {
        foreach ($this->nonmatches as $label => $nm) {
            print "$label: " . implode(', ', $nm) . "\n";
        }
    }

    function getArrayOfIndicesOfNonmatches($fieldRangeName)
    {
        $indices = array();
        foreach ($this->nonmatches as $label => $arr) {
            if ($label == $fieldRangeName) {
                foreach ($arr as $num) {
                    $ret = array_search($num, $this->nums);                
                    if ($ret !== false) {
                        $indices[] = $ret;
                    }
                }
            }
        }
        return $indices;
    }

    static function printYN($rangeName)
    {
        global $tickets;

        for ($ii = 0; $ii < count($tickets[0]->nums); $ii++) {
            $worksForEveryTicket = true;

            foreach ($tickets as $t) {
                if (!in_array($t->nums[$ii], $t->matches[$rangeName])) {
                    $worksForEveryTicket = false;
                }
            }

            print ($worksForEveryTicket) ? '  *' : '   ';
        }

        print "\n";
    }
}

$lines = explode("\n", file_get_contents('fields.txt'));
$ranges = array();

foreach ($lines as $line) {
    // arrival location: 42-636 or 642-962
    if (preg_match('/^(.*): (\d+)-(\d+) or (\d+)-(\d+)$/', $line, $m)) {
        $ranges[$m[1]] = array(new Range($m[2], $m[3]), new Range($m[4], $m[5]));
    } else {
        assert(false, $line);
    }
}

$lines = explode("\n", file_get_contents('nearby.txt'));
$tickets = array();

// Many of the ticket numbers will match many of the ranges for each field; this
// is the opposite of my naive expectation.  In fact, a lot of the ticket numbers
// match ALL the field ranges on a lot of tickets.  So instead of finding the matches,
// let's find the fields that don't match all the numbers and use that to narrow it down.

foreach ($lines as $ticketIndex => $line) {
    $nums = explode(',', $line);
    $t = new Ticket($nums);

    foreach ($nums as $num) {
        $inOneRange = false;
        foreach ($ranges as $name => $range) {
            if (($range[0]->contains($num) || $range[1]->contains($num))) {
                $t->addMatch($num, $name);
                $inOneRange = true;
            } else {
                $t->addNonmatch($num, $name);
            }
        }

        if (!$inOneRange) {
            $t->valid = false;            
        }
    }
    
    print "[Ticket $ticketIndex] $t\n";
    print "Nonmatches:\n";
    if (!$t->valid) {
        print "\tINVALID\n";
    } else {
        $tickets[] = $t;
        $t->printNonmatches();
    }
    print "\n";
}


// Print a chart of which field positions satisfy a given range on EVERY ticket.
// (We end up testing every field position/ticket combination.)  Using the 
// combinations that are not satisfied as restrictions, I built a spreadsheet
// with the order.  We only need the order of the "departure" fields.
print "X axis: ticket field positions\n";
printf('%32s', '');
for ($i = 0; $i < 20; $i++) { printf('%3d', $i); }
print("\n");
foreach ($ranges as $name => $range) {
    printf('%30s: ', $name);
    Ticket::printYN($name);
}
