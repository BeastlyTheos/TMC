<?php
include_once "sortTeams.php";

class pairing_state_machine {
	public static function create($teams, $context) {
		$matchStack = new SplStack();
		self::pairNewTeams($teams, $context, $matchStack);
		$matchArray = array();

		while ( $matchStack->count()) {
			$m = $matchStack->pop();
			$matchArray[] = $m;
		}//end converting stack to array

		return $matchArray;
	}//end function create


	private static function pairNewTeams($teams, $context, $matches) {
		$teamsByAverageness = $teams;
		usort($teamsByAverageness, "compareByAverageness");

//if there are teams with no games played, pair them against the teams closest to 500
		if ( 0 == $teamsByAverageness[0]->gamesPlayed ) {
			//find next team that has games played
			$current = $opponent = 0;
			while ( count($teamsByAverageness) > ++$opponent ) {
				$wp = $teamsByAverageness[$opponent]->wp;
				$gp = $teamsByAverageness[$opponent]->gamesPlayed;

				if ( 0 < $gp && 0 < $wp && $wp < 1 )
					break;
			}

			//while there is a team with no games played, and there is an opponent left to pair it with
			while ( 0 == $teamsByAverageness[$current]->gamesPlayed && count($teamsByAverageness) > $opponent ) {
				$wp = $teamsByAverageness[$current];
				$avgTeam = $teamsByAverageness[$current];
				$wp = $avgTeam->wp;
				$diff = $avgTeam->w - $avgTeam->l;
				$diff *= $diff;
				if ($wp ==1 || $wp == 0 || $diff > 1)
					break;
				//pair them
				$match = new Match(["home"=>$teamsByAverageness[$opponent], "away"=> $teamsByAverageness[$current]]);
				$match->home->hasMatch = $match->away->hasMatch = True;
				$matches->push($match);
				$current++;
				$opponent++;
			}
		}

		self::setBye($teams, $context, $matches);
		return $matches;
	}


	private static function setBye($teams, $context, $matches) {
		if ( count($teams) %2 == 1 ) {
			//find fewest number of byes recieved by a team that has played the maximum number of games
			$maxPlayed = 0;
			$maxByes = 0;
			$bye = new Team(["id"=>0, "name"=>"bye"]);

			foreach ( $teams as $team )
				if ( !$team->hasMatch) {
					$maxByes = $team->byes > $maxByes?  $team->byes:  $maxByes;
					$maxPlayed = $team->gamesPlayed > $maxPlayed?  $team->gamesPlayed:  $maxPlayed;
				}

			for ( $gp = $maxPlayed ;  $gp >= 0 ; $gp--)
				for ( $numByes = 0 ; $numByes <= $maxByes ; $numByes++ ) {
					//find the lowest-ranked team with the above number of byes and games played
					for ( $i = count($teams) -1 ;  $i >= 0 ; $i--)
						if ( $teams[$i]->gamesPlayed == $gp && $teams[$i]->byes == $numByes ) {
							$teams[$i]->hasMatch = true;
							$matches->Push( new Match(["home"=>$teams[$i], "away"=> $bye]));

							if ( self::pairTopTeams($teams, $context, $matches) )
								return $matches;
							else { //fixtures cannot be created with $team having the bye
								$match = $matches->pop();
								$match->home->hasMatch = $match->away->hasMatch = false;
							}//end else fixtures cannot be created with $team having the bye
						}//end if this team has $gp games played and $byes
				}//end iterating through teams with $gp games played and $numByes byes
		}//end if there is an odd number of teams
		else //there are not an odd number of teams
			self::pairTopTeams($teams, $context, $matches);

		return $matches;
	}//end function setBye


	private static function pairTopTeams($teams, $context, $matches) {
		//find top unpaired team
		for ( $top = 0 ; $top < count($teams) && $teams[$top]->hasMatch ; $top++ )
			;

//check for terminating condition
		if ( count($teams) == $top )
			return $matches;

//find heighest team that can play $top
		for ( $opponent = $top + 1 ; $opponent < count($teams) ; $opponent++ )
			if ( ! $teams[$opponent]->hasMatch && ! $teams[$top]->hasPlayed($teams[$opponent]->id, $context) ) { //if this is a valid pairing
				//create the match, then recurse to find complete set of pairings
				$match = new Match(["home"=>$teams[$top], "away"=> $teams[$opponent]]);
				$match->home->hasMatch = $match->away->hasMatch = True;
				$matches->push($match);

				if ( self::pairBottomTeams($teams, $context, $matches))
					return $matches;
				else { //could not find a valid set of fixtures for this pairing
					$match = $matches->pop();
					$match->home->hasMatch = $match->away->hasMatch = false;
				}//end could not find a valid set of fixtures for this pairing
			}//end if this pair can play one another

		return false;
	}//end pair top teams


	private static function pairBottomTeams($teams, $context, $matches) {
		//find bottom unpaired team
		for ( $bottom = count($teams) -1 ; $bottom >= 0 && $teams[$bottom]->hasMatch ; $bottom-- )
			;

//check for terminating condition
		if ( $bottom < 0 )
			return $matches;

//find lowest team that can play $bottom
		for ( $opponent = $bottom - 1 ; $opponent >= 0 ; $opponent-- )
			if ( ! $teams[$opponent]->hasMatch && ! $teams[$bottom]->hasPlayed($teams[$opponent]->id, $context) ) { //if this is a valid pairing
				//create the match, then recurse to find complete set of pairings
				$match = new Match(["home"=>$teams[$opponent], "away"=> $teams[$bottom]]);
				$match->home->hasMatch = $match->away->hasMatch = True;
				$matches->push($match);

				if ( self::pairTopTeams($teams, $context, $matches))
					return $matches;
				else { //could not find a valid set of fixtures for this pairing
					$match = $matches->pop();
					$match->home->hasMatch = $match->away->hasMatch = false;
				}//end could not find a valid set of fixtures for this pairing
			}//end if this pair can play one another

		return false;
	}//end pair bottom teams
}//end class
?>
