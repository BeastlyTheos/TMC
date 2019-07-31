<?php
include_once "Team.php";

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

public function __construct($h, $a)
    {$this->home = $h;
$this->home->hasMatch = true;
$this->away = $a;
$this->away->hasMatch = true;
    }//end constructor

function __destruct()
    { $this->home->hasMatch = false; $this->away->hasMatch = false; }

public static function createFromArray($data)
{
$match = new Match(new Team(0), new Team(0));
$match->o = self::extractFromArray($data, "o", "intval", 0);
if (!$match->o)
	$match->o = null;
$match->context = self::extractFromArray($data, "context", "intval", 0);
$match->id = self::extractFromArray($data, "id", "intval", 0);
$homeID = self::extractFromArray($data, "home", "intval", 0);
$match->home = Team::getByID($homeID);
$awayID = self::extractFromArray($data, "away", "intval", 0);
$match->away = Team::getByID($awayID);
$match->homegoals = self::extractFromArray($data, "homegoals", "intval", 0);
$match->awaygoals = self::extractFromArray($data, "awaygoals", "intval", 0);
$match->round = self::extractFromArray($data, "round", "intval", 0);
$match->type = self::extractFromArray($data, "type", "strval", 0);
$match->arena = self::extractFromArray($data, "arena", "intval", 0);
return $match;
}//end createFromArray

private static function extractFromArray($data, $attribute, $cast, $default)
{if (isset($data[$attribute]) && $data[$attribute] != null)
	return $cast($data[$attribute]);
return $cast($default);
}
}//end Match
?>
