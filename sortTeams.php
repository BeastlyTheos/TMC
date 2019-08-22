<?php
require_once "yoursql.php";
include_once "team.php";

function cmp($val1, $val2)
	{if ( $val1 < $val2 )
		return 1;
	if ( $val1 > $val2 )
		return -1;
	return 0;
	}

function compareTeams( $a, $b)
	{
	//sort by wp descending
	$delta = cmp($a->wp, $b->wp);
	if($delta)
		return $delta;
	//sort by wins over/under 500 descending
	$delta = cmp($a->w - $a->l, $b->w - $b->l);
	if($delta)
		return $delta;
	//sort by games played descending
	$delta = cmp($a->gamesPlayed, $b->gamesPlayed);
	if($delta)
		return $delta;
	//sort by goal ratio descending
	$delta = cmp($a->gr, $b->gr);
	if($delta)
		return $delta;
	//sort by goal differential descending
	$delta = cmp($a->gf-$a->ga, $b->gf-$b->ga);
	if($delta)
		return $delta;
	//sort by seed ascending
	return cmp($b->seed, $a->seed);
	}//end compare teams

function compareMatches( $a, $b)
{return compareTeams( $a->home, $b->home);}

function compareByAverageness($a, $b)
	{
	if ( $a->gamesPlayed && !$b->gamesPlayed )
		return 1;
	if ( !$a->gamesPlayed && $b->gamesPlayed )
		return -1;

	//sort by absolute distance from .500 ascending
	$delta = cmp(abs($b->wp-0.5), abs($a->wp-0.5));
	if($delta)
		return $delta;
	//sort by games played descending
	$delta = cmp($a->gamesPlayed, $b->gamesPlayed);
	if($delta)
		return $delta;
	//sort by absolute distance from even goal ratio ascending
	$delta = cmp(abs($b->gr-0.5), abs($a->gr-0.5));
	if($delta)
		return $delta;
	//sort by total goals ascending
	$delta = cmp($b->gf+$b->ga, $a->gf+$a->ga);
	if($delta)
		return $delta;
	//sort by seed ascending
	return cmp($b->seed, $a->seed);
	}//end compareByAverageness

function getTeams($context)
	{
	$res = yoursql_query("select standings.id, name, w+d+l as gp, (2*w+d)/(2*(w+d+l)) as wp, w, l, byes, gf/(gf+ga) as gr, gf, ga, seed from standings join teams on standings.id = teams.id where !inNationalCup and context = $context");
	$teams = array();

	while ( $team =  $res->fetch() )
		{//cast fields to the correct datatype
	if ( null == $team["wp"] )
			$team["wp"] = 0.5;
		if ( null == $team["gr"] )
			$team["gr"] = 0.5;
		foreach ( array("gp", "w", "l", "gf", "ga", "seed") as $field )
			$team[$field] = (int) $team[$field];
		foreach ( array("wp", "gr") as $field )
			$team[$field] = (float) $team[$field];
		$team = new Team($team);
		$teams[] = $team;
		}

	return $teams;
	}//end getTeams

function getStandings($context)
	{
	$teams = getTeams($context);

	usort($teams, "compareTeams");

	return $teams;
	}//end getStandings

/*
$teams = getTeams(17);
usort($teams, "compareByAverageness");
//$teams = getStandings(17);
//$teams = getTeamsByAverageness(17);
echo "<h1>teams</h1><table>\n";
$fields = array("name", "wp", "w", "l", "gr");
echo "<tr>\n";
echo "<th>name</th><th>wp</th><th>gr</th>";
echo "</tr>\n";
for ($i = 0 ; $i < count($teams) ; $i++)
{$t = $teams[$i];
echo "<tr>";
foreach ( $fields as $f )
	echo "<td>".$t->$f."</td>";
	echo "</tr>";
	}
echo "hi";
*/
?>
