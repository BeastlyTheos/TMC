<?php $debug = true;
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader, array("debug"=>$debug));

include "CHPPConnection.php";
include 'TournamentFunctions.php';

class MatchResult {
	public $homeName;
	public $awayName;
	public $homeGoals;
	public $awayGoals;
}

class UnplayedMatch {
	public $homeName;
	public $awayName;
}

$matchResults = array();
$unplayedMatches = array();

//get teams   and dates of matches with an ID, but not played
if(null != ($res = yoursql_query("select id from matches where id is not null and homegoals is null"))) { //and round < ".CupRoundOf($today).";")))
	while($r = $res->fetch()) {
		if($match = $HT->getSeniorMatchDetails($r['id'], false)) {
			$homeTeam = $match->getHomeTeam();
			$awayTeam = $match->getAwayTeam();

			if($match->getEndDate()) {
				$matchResult = new MatchResult();
				$matchResult->homeName = $homeTeam->getName();
				$matchResult->awayName = $awayTeam->getName();

				$duration = (new DateTime($match->getEndDate()))->diff(new DateTime($match->getStartDate()));
				$minutes = $duration->h*60 +$duration->i;
				$homeGoals = $homeTeam->getGoals();
				$awayGoals = $awayTeam->getGoals();
				if(120+15 < $minutes) { //if the match lasted longer than 90min + halftime break
					if($homeGoals > $awayGoals)
						$winner = "home";
					else
						$winner = "away";
					$homeGoals = $awayGoals = 0;
					for ($i = 1 ; $i <= $match->getTotalGoals() && 90 >= $match->getGoal($i)->getMinute() ; $i++) {
						if($homeTeam->getId() == $match->getGoal($i)->getScorerTeamId())
							$homeGoals++;
						else
							$awayGoals++;
					}//end of counting all goals
					if("home" == $winner) //add the extra goal for whomever won in overtime
						$homeGoals++;
					else
						$awayGoals++;
				}//end if match went into overtime
				$matchResult->homeGoals = $homeGoals;
				$matchResult->awayGoals = $awayGoals;
				$matchResults[] = $matchResult;
				yoursql_query("call PlayMatch(".$match->getId().", $homeGoals, $awayGoals)");
			} else { //has not been played yet
				$unplayedMatch = new UnplayedMatch();
				$unplayedMatch->homeName = $homeTeam->getName();
				$unplayedMatch->awayName = $awayTeam->getName();
				$unplayedMatches[] = $unplayedMatch;
			}
		}//end if match was found
	}//end while there's matches to be found
}//end if there are matches to be played

$twig->display("check_match_results.html", array("matchResults"=>$matchResults, "unplayedMatches"=>$unplayedMatches));
?>
