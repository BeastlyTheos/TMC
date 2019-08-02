<?php
include_once "Team.php";
include_once "TournamentFunctions.php";

$matchesCache = [];

class Match
{
public $o;
public $context;
public $id;
public $home;
public $away;
public $homegoals;
public $awaygoals;
public $round;
public $type;
public $arena;

public function __construct($data)
{global $context;

$this->o = self::extract($data, "o", null);
$this->context = self::extract($data, "context", $context);
$this->id = self::extract($data, "id", null);

$home = self::extract($data, "home", null);
if (gettype($home) == "object" && get_class($home) == "Team")
	$this->home = $home;
else
	$this->home = Team::getByID($home);

$away = self::extract($data, "away", null);
if (gettype($away) == "object" && get_class($away) == "Team")
	$this->away = $away;
else
	$this->away = Team::getByID($away);

$this->homegoals = self::extract($data, "homegoals", null);
$this->awaygoals = self::extract($data, "awaygoals", null);
$this->round = self::extract($data, "round", CurrentRound);
$this->type = self::extract($data, "type", null);
$this->arena = self::extract($data, "arena", null);

//legacy code needed for the pairingStateMachine
$this->home->hasMatch = true;
$this->away->hasMatch = true;
}//end constructor

function __destruct()
    { $this->home->hasMatch = false; $this->away->hasMatch = false; }

/* extract
 * params: array of data, key for the array, and a default value
 * returns:  either the value of data[key], or the default value
 */
private function extract($data, $key, $default)
{if (isset($data[$key]))
	if ("null" === $data[$key] || "" === $data[$key])
		return null;
	else
		return $data[$key];
else
	return $default;
}

public static function getByO($o)
{
$o = strval($o);
if (!isset($matchesCache[$o]))
	{if ($o)
		{$res = yoursql_query("select * from matches where o = $o");
		if (1 != $res->num_rows)
			throw new Exception("Tried to retrieve match with invalid o of $o");
		$data = $res->fetch_assoc();
		}//end if $o is nonzero
	else
		$data = [];
	$match = new Match($data);
	$matchesCache[$o] = $match;
	}//end if not set
return $matchesCache[$o];
}//end getByO

public function save()
{
$vals = [];
if ($this->home->id)
	$vals[] = "home = ".$this->home->id;
else
	$vals[] = "home = null";
	
if ($this->away->id)
	$vals[] = "away = ".$this->away->id;
else
	$vals[] = "away = null";
	
if ($this->id && $this->id != "0")
	$vals[] = "id = ".$this->id;
else
	$vals[] = "id = null";
$vals[] = "round = ".$this->round;
$vals[] = "context = ".$this->context;

$vals = join(", ", $vals);
if ($this->o)
	$query = "update matches set $vals where o = ".$this->o;
else
	$query = "insert into matches  set $vals";
	yoursql_query($query);
}//end save
}//end Match
?>
