<?php
$count = 0;
class Team
{
    public $id;
        public $rank;
    public $name;
public $wp;
public $gr;
public $gamesPlayed;
public $byes;
    public $hasMatch;

public function __construct($rank, $data)
{
$this->id = $data['id'];
$this->rank = $data['rank'];
$this->name = $data['name'];
$this->wp = $data['wp'];
if ( $this->wp === null )
	$this->wp = 0.5;
$this->gr = $data['gr'];
$this->gamesPlayed = $data['gp'];
$this->w = $data["w"];
$this->l = $data["l"];
$this->byes = $data['byes'];
$this->gf = $data["gf"];
$this->ga = $data["ga"];
$this->seed = $data["seed"];
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