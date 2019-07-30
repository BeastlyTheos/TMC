<?php
$count = 0;
$teamsCache = [];

class Team
{
    public $id;
    public $name;
public $wp;
public $gr;
public $gamesPlayed;
public $byes;
    public $hasMatch;

public function __construct($data)
{
$this->id = $data['id'];
if (!$this->id)
	$this->id = null;
$this->name = $data['name'];
$this->wp = self::extract($data, "wp", "floatval", 0.5);
$this->gr = self::extract($data, "gr", "floatval", 0.5);
$this->gamesPlayed = self::extract($data, "gp", "intval", 0);
$this->w = self::extract($data, "w", "intval", 0);
$this->d = self::extract($data, "d", "intval", 0);
$this->l = self::extract($data, "l", "intval", 0);
$this->byes = self::extract($data, "byes", "intval", 0);
$this->gf = self::extract($data, "gf", "intval", 0);
$this->ga = self::extract($data, "ga", "intval", 0);
$this->seed = self::extract($data, "seed", "intval", 0);
$this->hasMatch = false;
}//end constructor 

private function extract($data, $attribute, $cast, $default)
{if (isset($data[$attribute]) && $data[$attribute] != null)
	return $cast($data[$attribute]);
return $cast($default);
}

    public function hasPlayed($op, $context)
    {global $count;
    $query = "select CountMatchesBetween($this->id, $op, $context)";
if($count++ > 80)
{exit(); abort();}
return 0 != yoursql_query($query)->fetch_row()[0];
           }//end hasPlayed
}//end team class
?>