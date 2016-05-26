 <title>Play Matches</title>
 <?php
include "CHPPConnection.php";
include 'TournamentFunctions.php';

//global $HT, $today;

//get teams   and dates of matches with an ID, but not played
if(null != ($res = yoursql_query("select id from matches where id is not null and homegoals is null"))) //and round < ".CupRoundOf($today).";")))
{echo "<br/><h1>playing matchs</h1><br/>\n";

  while($r = $res->fetch_assoc())
	{if($match = $HT->getSeniorMatchDetails($r['id'], false))
		{if($match->getEndDate())
			{$duration = (new DateTime($match->getEndDate()))->diff(new DateTime($match->getStartDate()));
			$minutes = $duration->h*60 +$duration->i;
			$homeGoals = $match->getHomeTeam()->getGoals();
			$awayGoals = $match->getAwayTeam()->getGoals();
			if(90+15 < $minutes) //if the match lasted longer than 90min + halftime break
				{if($homeGoals > $awayGoals)
					$winner = "home";
				else
					$winner = "away";
				$homeGoals = $awayGoals = 0;
				for ($i = 1 ; $i <= $match->getTotalGoals() && 90 >= $match->getGoal($i)->getMinute() ; $i++)
					{if($match->getHomeTeam()->getId() == $match->getGoal($i)->getScorerTeamId())
						$homeGoals++;
					else
						$awayGoals++;
					}//end of counting all goals
				if("home" == $winner) //add the extra goal for whomever won in overtime
					$homeGoals++;
				else
					$awayGoals++;
				}//end if match went into overtime
			printf("<h2>playing %s verses %s by a score of %d-%d, which lasted %d minutes.</h2>\n", $match->getHomeTeam()->getName(), $match->getAwayTeam()->getName(), $homeGoals, $awayGoals, $minutes);
			yoursql_query("call PlayMatch(".$match->getId().", $homeGoals, $awayGoals)");
			}
		else //has not been played yet
			printf("<h2>unplayed: %s verses %s</h2>\n", $match->getHomeTeam()->getName(), $match->getAwayTeam()->getName());
		}//end if match was found
	else
		printf("<h2>CHPP Error finding match %d</h2><br/>\n", $r['id']);
	}//end while there's matches to be found
	echo "<br/>\n";
	}//end if there are matches to be played
?>