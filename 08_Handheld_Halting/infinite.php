<?php

// Copyright 2020 Max Sprauer

$emu = new Emulator();
$lines = explode("\n", file_get_contents('input.txt'));
$visited = array();

do {
    list($instruction, $argument) = explode(' ', $lines[$emu->pc]);

    $emu->$instruction($argument);

    print "{$emu->pc}\n";
    if (in_array($emu->pc, $visited)) {
        break;
    } else {
        $visited[] = $emu->pc;
    }
} while(1);

print "Part One: {$emu->acc}\n";


// Try flipping each jmp and nop, one at a time.  I tried to be a bit smarter by looking
// for a nop argument that would jump to the end, but that didn't work.  Also tried tracing
// jumps back to avoid a loop, but that didn't pan out.  Going brute force, which is fast enough.
for ($ii = 0; $ii < count($lines); $ii++)
{
    $acc = executeWithLoopDetect($ii);
    if ($acc >= 0) {
        print "Part Two: Executed after changing $ii.  Acc = $acc.\n";
        exit;
    }
}

class Emulator
{
    public $acc = 0;
    public $pc = 0;

    function acc($x)
    {
        $this->acc = eval("return {$this->acc} $x;");
        $this->pc++;
    }

    function jmp($x)
    {
        $this->pc = eval("return {$this->pc} $x;");
    }

    function nop($x) 
    {
        $this->pc++;
    }
}

// Returns -1 if loop detected or ACC value if program ends normally.
// If ACC actually is -1 we'll never know!
function executeWithLoopDetect($manipulate = null)
{
    $emu = new Emulator();
    $lines = explode("\n", file_get_contents('input.txt'));
    $visited = array();

    do {
        list($instruction, $argument) = explode(' ', $lines[$emu->pc]);

        if ($manipulate === $emu->pc) {
            switch ($instruction) {
                case 'jmp': $instruction = 'nop'; break;
                case 'nop': $instruction = 'jmp'; break;
                case 'acc': return -1;
            }
        }

        $emu->$instruction($argument);

        if (in_array($emu->pc, $visited)) {
            return -1;
        } else {
            $visited[] = $emu->pc;
        }
    } while($emu->pc < count($lines));

    return $emu->acc;
}