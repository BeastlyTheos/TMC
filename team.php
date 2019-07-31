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

public static function getByID($id)
{$id = strval($id);
if (!isset($teamsCache[$id]))
	{if ($id)
		{$res = yoursql_query("select * from teams where id = $id");
		if (1 != $res->num_rows)
			throw new Exception("Tried to retrieve team with invalid id of $id");
		$data = $res->fetch_assoc();
		}
	else
		$data = ["id"=>0, "name"=>"bye"];
	$team = new Team($data);
	$teamsCache[$id] = $team;
	}//end creating team
return $teamsCache[$id];
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