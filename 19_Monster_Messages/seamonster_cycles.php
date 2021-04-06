<?php

// Copyright 2021 Max Sprauer
// I thought about just generating every possible message and comparing, but I didn't do it that way.
// It turns out that the output would have been super huge with the cycles in Part Two.

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
        // Rules 8 and 11 are only used by themselves and Rule 0.
        // The greedy approach does not seem to work.  Since Rule 0 is "8 11", we might have to try
        // different numbers of iterations of Rule 8 before letting Rule 11 have its turn.

        // Rule 42 consumes 5 characters per run.  So I'm going to be a little lazy here and limit
        // the Rule 8 runs to string length / 5, which should be more than enough.

        $rule8Limit = intdiv(strlen($this->message), 5);

        while ($rule8Limit > 0) {
            print "*** Trying with Rule 8 Limit: $rule8Limit\n";
            $this->rule8Limit = $rule8Limit;
            $c = $this->evaluateRule(0, 0, 0);
            if ($c == strlen($this->message)) {
                return $c;
            }

            $rule8Limit--;
        }

        return 0;
    }

    // Returns 0 on failure or number of message characters consumed 
    function evaluateANDSeries($series, $msgOffset, $depth)
    {
        // 1 4 5
        $ret = 0;
        
        $ids = explode(' ', trim($series));
        foreach ($ids as $childId) {
            $result = $this->evaluateRule($childId, $msgOffset + $ret, $depth + 1);

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
    function evaluateRule($id, $msgOffset = 0, $depth = 0)
    {
        global $RULES;
        $ret = 0;
        $indent = str_repeat("   ", $depth);

        assert(isset($RULES[$id]));
        $ruleText = $RULES[$id];

        print "$indent Rule $id: $ruleText " . substr($this->message, $msgOffset) . "\n";

        if ($msgOffset >= strlen($this->message)) {
            // If we run out of characters in the middle of a rule, the rule fails
            print "msgOffset = $msgOffset, strlen = " . strlen($this->message) . "\n";
            return 0;
        }

        if (8 == $id) {
            // 8: 42 | 42 8
            // 8 can be one more 42's
            // Loop here instead of recursing
            $loopCount = 0;
            $c = 0;

            do {
                $c = $this->evaluateRule(42, $msgOffset + $ret, $depth + 1);
                if ($c > 0) {
                    $loopCount++;
                    $ret += $c;
                }
            } while ($c > 0 && $loopCount < $this->rule8Limit);

            if ($loopCount > 0) {
                print "Rule $id: Rule 42 consumed $ret characters in $loopCount loops.\n";                
            }
        } else if (11 == $id) {
            // 11: 42 31 | 42 11 31
            // 11 can be one or more 42's followed by the same number of 31's
            // Loop here instead of recursing
            $loopCount = 0;
            $consumed = 0;
            $c = 0;

            // First count how many times we can apply rule 42
            do {
                $c = $this->evaluateRule(42, $msgOffset + $consumed, $depth + 1);
                if ($c > 0) {
                    $loopCount++;
                    $consumed += $c;
                }
            } while ($c > 0);

            if ($loopCount > 0) {
                print "Rule $id: Rule 42 consumed $consumed characters in $loopCount loops.\n";                

                // Now try to apply rule 31 the same number of times
                do {
                    $c = $this->evaluateRule(31, $msgOffset + $consumed, $depth + 1);
                    if ($c > 0) {
                        $loopCount--;
                        $consumed += $c;
                    }
                } while ($loopCount > 0 && $c > 0);

                if (0 == $loopCount && $c > 0) {
                    // Succeeded in applying rule 31 the same number of times
                    print "Rule $id: Rule 31 total $consumed characters\n";                
                    $ret += $consumed;
                }
            }
        } else if (preg_match('/"([a-z])"/', $ruleText, $m)) {
            $letter = $m[1];
            if (!isset($this->message[$msgOffset])) {
                assert(0);
            }

            if ($this->message[$msgOffset] == $letter) {
                print "$indent Rule $id can consume $letter\n";
                $ret = 1;
            }
        } else if (false !== strpos($ruleText, '|')) {
            $subrules = explode('|', trim($ruleText));
            for ($i = 0; $i < count($subrules); $i++) {
                // Stop when we find one that works
                $subrule = $subrules[$i];
                $val = $this->evaluateANDSeries($subrule, $msgOffset, $depth);
                if ($val > 0) {
                    $ret = $val;
                    break;
                }

                if ($i != count($subrules) - 1) {
                    print "$indent Rule $id: Switching OR paths\n";
                }
            }
        } else {
            // "4 1 5" case -- evaluate rules, AND results together
            // return value will be 0 if it failed, so $ret will be 0
            $ret += $this->evaluateANDSeries($ruleText, $msgOffset, $depth);
        }

        if ($ret && (in_array($id, [42,31]))) {
            // print "Rule $id matched " . substr($this->message, $msgOffset, $ret) . "\n";
        }

        // if ($ret) {
            // print str_repeat(' ',  $msgOffset) . substr($this->message, $msgOffset) . " Rule $id: $ruleText, \$ret = $ret\n";
            // print str_repeat(' ', $depth) . "Rule $id: $ruleText, \$ret = $ret\n";
        // }

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

print "Part Two Matches: $matchCount\n";

