<title>Get match IDs</title>
<?php
include 'CHPPConnection.php';
include 'TournamentFunctions.php';
define("dateFormat", "Y-m-d");

echo "<h1>Getting match IDs</h1><br/>\n";
//get teams   and round of matches with no ID, ordering them by earlier matches first
$res = yoursql_query("select home, away, context, round   from matches where type = 'match' and id is null order by round asc");
  
//find the ID of every match
 while($r = $res->fetch_assoc()) //for each match  in the database
	{if($matches = getTeamMatchesByRound($r["home"], 1+$r["round"])) //if CHPP returns data for this match
		{$homeTeamHasFriendly = $awayTeamHasFriendly = $isMatchScheduled = false;
		for($i = 1 ; $i <= $matches->getNumberMatches() ; $i++)
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
						{printf("<h2>Swapping venues for %s and %s</h2<br/>\n", $r["home"], $r["away"]);
						yoursql_query("call SwapVenue(".$r['home'].", ".$r['away'].", ".$r['context'].", ".$r['round'].")");
						}//end swapping venues
					printf("</h3>Setting %d as match id for %s verses %s.</h3>\n", $m->getId(), $m->getHomeTeamName(), $m->getAwayTeamName());
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
			else
				printf("<h2>didn't find any away matches for %s</h2></br>\n", HTMatchesURL($r["away"]));
			
			printf("match was not scheduled. home %s. away %s<br/>\n", $homeTeamHasFriendly? "true": "false", $awayTeamHasFriendly? "true": "false");
			if($homeTeamHasFriendly)
				if($awayTeamHasFriendly)
					printf("Both %s and %s have friendlies against wrong oppponents.<br/>\n", HTMatchesURL($r["home"]), HTMatchesURL($r["away"]));
				else //away does not have a friendly
					{printf("%s forfits to %s<br/>\n", HTMatchesURL($r["home"]), HTMatchesURL($r["away"]));
					yoursql_query("call forfit( ${r['home']}, ${r["context"]}, ${r['round']})");
					}//end home team has friendly
			else //home team does not have friendly
				if($awayTeamHasFriendly)
					{printf("%s forfits to %s<br/>\n", HTMatchesURL($r["away"]), HTMatchesURL($r["home"]));
					yoursql_query("call forfit(${r['away']}, ${r['context']}, ${r['round']})");
					}//end away team has friendly
				else //away does not have a friendly
					printf("neither %s nor %s have friendlies.<br/>\n", HTMatchesURL($r["home"]), HTMatchesURL($r["away"]));
			}//end if match was not scheduled
		}//end if CHPP returned home data
	else //CHPP did not find data
		printf("CHPP returned no home matches for %s verses %s<br/>\n", $r["home"], $r["away"]);
	}//end looping through hmatches in the db

if( ! $res->num_rows)
	echo 'no matchIds to be found';

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
?>