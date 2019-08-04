<?php
$teamsCache = [];

class Team
{
    public $id;
    public $name;
public $wp;
public $gr;
public $gamesPlayed;
public $byes;
    public $hasMatch; //for backwards compatibility with the pairingStateMachine

public function __construct($data)
{
$this->id = $data['id'];
if (!$this->id)
	$this->id = null;
$this->name = $data['name'];
$this->wp = self::extract($data, "wp", 0.5);
$this->gr = self::extract($data, "gr", 0.5);
$this->gamesPlayed = self::extract($data, "gp", 0);
$this->w = self::extract($data, "w", 0);
$this->d = self::extract($data, "d", 0);
$this->l = self::extract($data, "l", 0);
$this->byes = self::extract($data, "byes", 0);
$this->gf = self::extract($data, "gf", 0);
$this->ga = self::extract($data, "ga", 0);
$this->seed = self::extract($data, "seed", 0);
$this->hasMatch = false;
}//end constructor 

/* extract
 * params: array of data, key for the array, function that casts to a datatype, and default value
 * returns:  either the value of data[key], or the default value
 */
private function extract($data, $key, $default)
{if (isset($data[$key]))
	if ("null"==$data[$key])
		return null;
	else
		return $data[$key];
else
	return $default;
}

/* getByID
 * param: id of the requested team
 *returns: singleton of a team object
 */
public static function getByID($id)
{$id = strval($id);
if (!isset($teamsCache[$id]))
	{if ($id)
		{$res = yoursql_query("select * from teams where id = $id");
		if (1 != $res->rowCount())
			throw new Exception("Tried to retrieve team with invalid id of $id");
		$data = $res->fetch();
		}
	else
		$data = ["id"=>0, "name"=>"bye"];
	$team = new Team($data);
	$teamsCache[$id] = $team;
	}//end creating team
return $teamsCache[$id];
}

/* hasPlayed
 * params: id of another team, and context id
 * returns boolean indicating if the two teams have played before in the given context
 */
public function hasPlayed($op, $context)
{$query = "select CountMatchesBetween($this->id, $op, $context)";
return 0 != yoursql_query($query)->fetch()[0];
}//end hasPlayed
}//end team class
?>