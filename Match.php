<?php
class Match
{
    public $home;
    public $away;

    public function __construct($h, $a)
    {$this->home = $h;
$this->home->hasMatch = true;
$this->away = $a;
$this->away->hasMatch = true;
    }//end constructor

function __destruct()
    { $this->home->hasMatch = false; $this->away->hasMatch = false; }
}//end Match
?>