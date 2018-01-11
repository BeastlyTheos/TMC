<?php
$count = 0;
class Team
{
    public $id;
        public $rank;
    public $name;
public $gamesPlayed;
public $byes;
    public $hasMatch;

public function __construct($id, $rank, $name, $gp, $byes)
    {
        $this->id = $id;
        $this->rank = $rank;
        $this->name = $name;
$this->gamesPlayed = $gp;
$this->byes = $byes;
$this->hasMatch = false;
}//end constructor 

    public function hasPlayed($op, $context)
    {global $count;
    $query = "select CountMatchesBetween($this->id, $op, $context)";
if($count++ > 80)
{exit(); abort();}
return 0 != yoursql_query($query)->fetch_row()[0];
           }//end hasPlayed
}//end team class
?>