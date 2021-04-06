<?php

// Copyright 2021 Max Sprauer

$ruleLines = explode("\n", file_get_contents($argc == 3 ? $argv[1] : 'rules.txt'));
$messages = explode("\n", file_get_contents($argc == 3 ? $argv[2] : 'input.txt'));

$RULES = array();
foreach ($ruleLines as $l)
{
    list($id, $text) = explode(': ' , $l);
    $RULES[$id] = $text;
}

class Parser
{
    public $message;

    function __construct($m)
    {
        $this->message = $m;
    }

    function evaluate()
    {
        return $this->evaluateRule(0);
    }

    // Returns 0 on failure or number of message characters consumed 
    function evaluateANDSeries($series, $msgOffset)
    {
        // 1 4 5
        $ret = 0;
        
        $ids = explode(' ', trim($series));
        foreach ($ids as $childId) {
            $result = $this->evaluateRule($childId, $msgOffset + $ret);

            if (0 == $result) {
                // This AND series failed, no characters consumed
                return 0;
            } else {
                $ret += $result;
            }
        }

        return $ret;
    }

    // cases to handle:
    //    1) "a" one letter
    //    2) "4 1 5" a series of rules ANDed together
    //    3) "4 5 | 5 4" two series of rules ORed together -- these should only consume characters
    //          if the series is fully used.
    // returns number of message characters consumed
    function evaluateRule($id, $msgOffset = 0)
    {
        global $RULES;
        $ret = 0;

        assert(isset($RULES[$id]));
        $ruleText = $RULES[$id];

        if (preg_match('/"([a-z])"/', $ruleText, $m)) {
            $letter = $m[1];
            if ($this->message[$msgOffset] == $letter) {
                $ret = 1;
            }
        } else if (false !== strpos($ruleText, '|')) {
            $subrules = explode('|', trim($ruleText));
            foreach ($subrules as $subrule) {
                // Stop when we find one that works
                $val = $this->evaluateANDSeries($subrule, $msgOffset);
                if ($val > 0) {
                    $ret = $val;
                    break;
                }
            }
        } else {
            // "4 1 5" case -- evaluate rules, AND results together
            // return value will be 0 if it failed, so $ret will be 0
            $ret += $this->evaluateANDSeries($ruleText, $msgOffset);
        }

        return $ret;
    }
}
 
$matchCount = 0;

foreach ($messages as $message) {
    $p = new Parser($message);
    $consumed = $p->evaluate();
    $match = ($consumed == strlen($message));
    print "$message => " . ($match ? 'true' : 'false') . "\n";

    if ($match) {
        $matchCount++;
    }    
}

print "Part One Matches: $matchCount\n";

