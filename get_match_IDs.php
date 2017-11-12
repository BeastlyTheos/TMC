<?php $debug = true;
require_once 'C:/xampp/vendor/autoload.php';
  $loader = new Twig_Loader_Filesystem('templates');
 $twig = new Twig_Environment($loader, array("debug"=>$debug));
 
include 'CHPPConnection.php';
include 'TournamentFunctions.php';
define("dateFormat", "Y-m-d");

class MatchStatus
	{public $home;
	public $away;
	public $status;
	}

$matchStatuses = array();

//get teams   and round of matches with no ID, ordering them by earlier matches first
$res = yoursql_query("select home, away, h.name as homeName, a.name as awayName, context, round   from matches, teams as h, teams as a  where h.id = home and away = a.id and type = 'match' and matches.id is null order by round asc");
  
//find the ID of every match
 while($r = $res->fetch_assoc()) //for each match  in the database
	{$matchStatus = new MatchStatus();
	$matchStatus->home = $r["homeName"];
	$matchStatus->away = $r["awayName"];
	
	if($matches = getTeamMatchesByRound($r["home"], 1+$r["round"])) //if CHPP returns data for this match
		{$homeTeamHasFriendly = $awayTeamHasFriendly = $isMatchScheduled = false;
		//for($i = 1 ; $i <= $matches->getNumberMatches() ; $i++)
		//printf("%s verses %s", $matches->getMatch($i)->getHomeTeamName(), $matches->getMatch($i)->getAwayTeamName());
		
		/* find the HT matches of home, and cycle through them */
		for ($i = $matches->getNumberMatches() ; !$homeTeamHasFriendly &&   null != ($m = $matches->getMatch($i)) && $m->getDate() >= startOfNationalCupWeek($r["round"])->format(dateFormat) ; $i--) //for each match
			{//echo '<br/>'.$m->getDate().' >= '. startOfNationalCupWeek($r["round"])->format(dateFormat);
			////printf("considering %s verses %s<br/>", $m->getHomeTeamName(), $m->getAwayTeamName());
			//echo $m->getType().' should be 4<br/>';
			//echo $m->getType().' should be les than 9<br/>';
			//echo $m->getType().' should not be 9<br/>';
			if(4 <= $m->getType() && $m->getType() <= 9 && $m->getType() != 7) //if is a friendly
				{$homeTeamHasFriendly = true;
				if ($m->getAwayTeamId() == $r["away"] || $m->getHomeTeamId() == $r["away"] ) //if opponent is correct
					{$isMatchScheduled = true;
					if ( $m->getHomeTeamId() == $r["away"]) //if the actual home team is the scheduled away team
						{$temp = $matchStatus->home;
						$matchStatus->home = $matchStatus->away;
						$matchStatus->away = $temp;
						$matchStatus->status = "Swapped venues. ";

						yoursql_query("call SwapVenue(".$r['home'].", ".$r['away'].", ".$r['context'].", ".$r['round'].")");
						}//end swapping venues
					$matchStatus->status = $matchStatus->status . sprintf("Setting %s as match id for %s verses %s.", $m->getId(), $m->getHomeTeamName(), $m->getAwayTeamName());
					yoursql_query("call setMatchId(".$m->getId().", ".$m->getHomeTeamId().", ".$m->getAwayTeamId().", ".$r['context'].", ".$r['round'].')');
					}//end if found correct match
				}//end if is a friendly
			}//end for each HT 			 matche for home team
		
		/* if the match has not been found, check if away team has a friendly */
		if (  ! $isMatchScheduled)
			{if($matches = getTeamMatchesByRound($r["away"], $r["round"]))
				{
				/* find away's HT matches, and cycle through them */
				for ($i = $matches->getNumberMatches() ; ! $awayTeamHasFriendly && null != ($m = $matches->getMatch($i)) && $m->getDate() >= startOfNationalCupWeek($r["round"]) ; $i--) //for each match
					{$m = $matches->getMatch($i);
					//printf("considering the match between %s and %s<br/>\n", $m->getHomeTeamName(), $m->getAwayTeamName());
					if(4 <= $m->getType() && $m->getType() <= 9 && $m->getType() != 8) //if is a friendly
						$awayTeamHasFriendly = true;
					}//end for each of away's  HT matches
				}//end if chpp found matches for away team

			if($homeTeamHasFriendly)
				if($awayTeamHasFriendly)
					$matchStatus->status = sprintf("Both %s and %s have friendlies against wrong oppponents.<br/>\n", HTMatchesURL($r["home"]), HTMatchesURL($r["away"]));
				else //away does not have a friendly, but home has one
					{$matchStatus->status = sprintf("%s forfits to %s<br/>\n", HTMatchesURL($r["home"]), HTMatchesURL($r["away"]));
					yoursql_query("call forfit(${r['home']}, ${r['context']}, ${r['round']})");
					}//end away does not have a friendly, but home has one
			else //home does not have a friendly
				if($awayTeamHasFriendly)
					{$matchStatus->status = sprintf("%s forfits to %s<br/>\n", HTMatchesURL($r["away"]), HTMatchesURL($r["home"]));
					yoursql_query("call forfit(${r['away']}, ${r['context']}, ${r['round']})");
					}//end home does not have a friendly, but away has one
				else //neither has a friendly
					$matchStatus->status = sprintf("neither %s nor %s have friendlies.<br/>\n", HTMatchesURL($r["home"]), HTMatchesURL($r["away"]));
			}//end if match was not scheduled
		}//end if CHPP returned home data
		
	$matchStatuses[] = $matchStatus;
	}//end looping through hmatches in the db

function startOfNationalCupWeek($weekNum)
{global $startOfNationalCup;
$weekNum--; //decrease the week by one since week x is only x-1 weeks after start of season
return $startOfNationalCup->modify("+".$weekNum." weeks");
}//end startOfNationalCupWeek

function HTMatchesURL($id)
{return "<a target='_blank' href=\"http://hattrick.org/goto.ashx?path=/Club/Matches/?TeamID=".$id."\">".$id."</a>";}

$teamMatches = array(); 
function getTeamMatchesByRound( $id, $round = CurrentRound)
{global $HT, $teamMatches;
if( ! isset($teamMatches[$id][$round]) || null === $teamMatches[$id][$round])
	$teamMatches[$id][$round] = $HT->getSeniorTeamMatches($id, startOfNationalCupWeek($round+1)->format(dateFormat)); 
	return $teamMatches[$id][$round];
	}

$twig->display("get_match_IDs.html", array("matchStatuses"=>$matchStatuses));
?>
