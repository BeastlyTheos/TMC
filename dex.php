<title>TMC</title><?php
include "CHPPConnection.php";
include 'TournamentFunctions.php';

$HT->getTeam(1676757);

//FindArenaIds();
//yoursql_query("call compileStandings");
//PrintSQL("call getStandings(3)");
//PrintSQL("show create procedure getStandings");
//echo CreateHTLiveURL();
//UpdateTeamsStaticDataByEdition( 3);
//ContextualiseTeams( yoursql_query("select teams.id, seed from teams, //entrants where teams.id = entrants.id and edition = 3 order by seed limit //12,12"), 8);

function ContextualiseTeams( $res, $context) {
	while ( $r = $res->fetch())
		yoursql_query("call contextualise_team( ${r['id']}, $context, ${r['seed']})");
}

function UpdateTeamsStaticDataByEdition( $e) {
	global $HT, $sql;
	$res = yoursql_query(" select id from entrants where edition = $e");

	while ( $r = $res->fetch()) {
		$t = $HT->getTeam($r['id']);
		$name = mysqli_real_escape_string( $sql,  $t->getTeamName());
		echo $r['id'].' '.$name."<br/>";
		$region = $t->getRegionId();
		$arena = $t->getArenaId();
		$isBot = $t->isBot()? 1: 0;
		yoursql_query(" update teams set name = '$name', region = $region where id = ".$r['id']);
		yoursql_query(" update arenas set region = $region, isBot = $isBot, team = ".$r['id']." where id = $arena");
	}
}

/*function getMatchIds()
{echo "<h1>Getting match IDs</h1><br/>\n";
//get teams   and round of matches with no ID, ordering them by earlier matches first
$res = yoursql_query("select home, away, round   from matches where id is null order by round asc");

//find the ID of every match
  while($r = $res->fetch()) //for each match
	{if($matches = getTeamMatchesByRound($r["home"], $r["round"]))
		{$homeTeamHasFriendly = false; $isMatchScheduled = false;
		for ($i = $matches->getNumberMatches() ; !$homeTeamHasFriendly &&   null != ($m = $matches->getMatch($i)) && $m->getDate() >= startOfNationalCupWeek($r["round"])->format(dateFormat) ; $i--) //for each match
			{//$m = $matches->getMatch($i);
			if(4 <= $m->getType() && $m->getType() <= 9 && $m->getType() != 9) //if is a friendly
				{$homeTeamHasFriendly = true;
				if ($m->getAwayTeamId() == $r["away"] || $m->getHomeTeamId() == $r["away"] ) //if opponent is correct
					{$isMatchScheduled = true;
					if ( $m->getHomeTeamId() == $r["away"]) //if the actual home team is the scheduled away team
						{printf("<h2>Swapping venues for %s and %s</h2<br/>\n", $r["home"], $r["away"]);
						yoursql_query("call SwapVenue(".$r['home'].", ".$r['away'].", ".$r['round'].")");
						}
					printf("</h3>Setting %d as match id for %s verses %s.</h3>\n", $m->getId(), $m->getHomeTeamName(), $m->getAwayTeamName());
					yoursql_query("call setMatchId(".$m->getId().", ".$m->getHomeTeamId().", ".$m->getAwayTeamId().", ".$r['round'].");");
					}//end if found correct match
				}//end if is a friendly
			}//end for each match
		}//end if found at least one match
	else
		printf("<h2>didn't find any matches for %s</h2></br>\n", HTClubURL($r["home"]));
	if (  ! $isMatchScheduled)
		{if($matches = getTeamMatchesByRound($r["away"], $r["round"]))
			{$awayTeamHasFriendly = false;
			for ($i = $matches->getNumberMatches() ; ! $awayTeamHasFriendly && null != ($m = $matches->getMatch($i)) && $m->getDate() >= startOfNationalCupWeek($r["round"]) ; $i--) //for each match
				{$m = $matches->getMatch($i);
				printf("considering the match between %s and %s<br/>\n", $m->getHomeTeamName(), $m->getAwayTeamName());
				if(4 <= $m->getType() && $m->getType() <= 9 && $m->getType() != 8) //if is a friendly
					$awayTeamHasFriendly = true;
				}//end for each match
			}//end if chpp found matches
		else
			printf("<h2>didn't find any matches for %s</h2></br>\n", HTClubURL($r["away"]));
		if($homeTeamHasFriendly)
			if($awayTeamHasFriendly)
				printf("Both %s and %s have friendlies against wrong oppponents.<br/>\n", HTClubURL($r["home"]), HTClubURL($r["away"]));
			else //away does not have a friendly
				{printf("%s forfits to %s<br/>\n", HTClubURL($r["home"]), HTClubURL($r["away"]));
				yoursql_query("call forfit(".$r['home'].", ".$r["round"].")");
			}//end home team has friendly
		else //home team does not have friendly
			if($awayTeamHasFriendly)
				{printf("%s forfits to %s<br/>\n", HTClubURL($r["away"]), HTClubURL($r["home"]));
				yoursql_query("call forfit(".$r['away'].", ".$r["round"].")");
				}//end away team has friendly
			else //away does not have a friendly
				printf("neither %s nor %s have friendlies.<br/>\n", HTClubURL($r["home"]), HTClubURL($r["away"]));
		}//end if match is not scheduled
	}//end looping through scheduled matches

	echo "<br/>\n";
}//end getMatchIds
*/


/*function CheckCupEliminations()
{echo "<br/><h1>checking Who has been eliminated from the cup</h1><br/>\n";
global $HT;

  $teamsOutOfCup = array();

    if($res = yoursql_query("select id from teams where inNationalCup;")) //if there's a team in the national cup
	{echo "There are ".$res->rowCount()." teams in the national cup<br/>";
	while($r = $res->fetch()) //for each team
		                    {
		$isInCup = false; $hasOngoingMatch= false;
		                    $matches = GetTeamMatchesByRound($r['id']);
		                    echo "Considering team ".$r['id']." who has ".$matches->getNumberMatches()." matches<br/>";
		for($i = $matches->getNumberMatches() ; !$isInCup && $i >0 ; $i--)
			                    if($matches->getMatch($i)->getType() == 3)
				                    {$haveFoundCupMatch = true;
				                    printf("note: the following line appears more often than it logically should: %s is playing a cup match<br/>\n", HTClubURL($r['id']));
				                    }
		                  if(!$isInCup)
			                  				$teamsOutOfCup[count($teamsOutOfCup)] = $r['id'];
		                       }//end while  there's a team left
	                       if(count($teamsOutOfCup)) //if there was a team that exited the cup
		                       yoursql_query("update teams set inNationalCup = 0 where id in (".implode(", ", $teamsOutOfCup).")");
	               }//end if there's a team in the national cup
    else
	            echo "<h2>no teams were in the cup to begin with.</h2><br/>\n";
            echo "<br/>\n";
}                        //end RealiseWhoIsOutOfCup
*/

function GetMailURL($username) {
	return 'http://hattrick.org/goto.ashx?path=/MyHattrick/Inbox/?actionType=newMail%26alias='.$username;
}
?>

