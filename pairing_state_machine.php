<?php
class pairing_state_machine
{
public static function create($teams)
{
$matches = new SplStack();

if ( count($teams) %2 == 1 )
	{
	//find fewest number of byes recieved by a team that has played the maximum number of games
	$maxPlayed = 0;
	$maxByes = 0;
	$bye = new Team(0, 0, "bye", 0, 0);

	foreach ( $teams as $team )
		{
		$maxByes = $team->byes > $maxByes?  $team->byes:  $maxByes;
		$maxPlayed = $team->gamesPlayed > $maxPlayed?  $team->gamesPlayed:  $maxPlayed;
		}

	for ( $gp = $maxPlayed ;  $gp >= 0 ; $gp--)
		for ( $numByes = 0 ; $numByes <= $maxByes ; $numByes++ )
			{//find the lowest-ranked team with the above number of byes and games played
			for ( $i = count($teams) -1 ;  $i >= 0 ; $i--)
				if ( $teams[$i]->gamesPlayed == $gp && $teams[$i]->byes == $numByes )
					{
					$teams[$i]->hasMatch = true;
					$matches->Push( new Match($teams[$i], $bye));

					if ( self::pairTopTeams($teams, $matches) )
						return $matches;
					else //fixtures cannot be created with $team having the bye
						{
						$match = $matches->pop();
						$match->home->hasMatch = $match->away->hasMatch = false;
						}//end else fixtures cannot be created with $team having the bye
					}//end if this team has $gp games played and $byes
			}//end iterating through teams with $gp games played and $numByes byes
	}//end if there is an odd number of teams
else //there are not an odd number of teams
	{
	pairTopTeams($teams, $matches);
	return $matches;
	}//end if their are an even number of teams
}//end function create


private static function pairTopTeams($teams, $matches)
{//find top unpaired team
return true;
}//end pair top teams
}//end class
?>
