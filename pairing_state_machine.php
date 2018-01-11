<?php
class pairing_state_machine
{
public static function create($teams, $context)
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

					if ( self::pairTopTeams($teams, $context, $matches) )
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
	self::pairTopTeams($teams, $context, $matches);
	return $matches;
	}//end if their are an even number of teams
}//end function create


private static function pairTopTeams($teams, $context, $matches)
{//find top unpaired team
for ( $top = 0 ; $top < count($teams) && $teams[$top]->hasMatch ; $top++ )
	;
	
//check for terminating condition
if ( count($teams) == $top )
	return $matches;
	
//find heighest team that can play $top
for ( $opponent = $top + 1 ; $opponent < count($teams) ; $opponent++ )
	if ( ! $teams[$opponent]->hasMatch && ! $teams[$top]->hasPlayed($teams[$opponent]->id, $context) ) //if this is a valid pairing
		{//create the match, then recurse to find complete set of pairings
		$teams[$top]->hasMatch = $teams[$opponent]->hasMatch = true;
		$match = new Match($teams[$top], $teams[$opponent]);
		$matches->push($match);

		if ( self::pairBottomTeams($teams, $context, $matches))
			return $matches;
		else //could not find a valid set of fixtures for this pairing
			{
			$match = $matches->pop();
			$match->home->hasMatch = $match->away->hasMatch = false;
			}//end could not find a valid set of fixtures for this pairing
		}//end if this pair can play one another

return false;
}//end pair top teams


private static function pairBottomTeams($teams, $context, $matches)
{//find bottom unpaired team
for ( $bottom = count($teams) -1 ; $bottom >= 0 && $teams[$bottom]->hasMatch ; $bottom-- )
	;
	
//check for terminating condition
if ( $bottom < 0 )
	return $matches;
	
//find lowest team that can play $bottom
for ( $opponent = $bottom - 1 ; $opponent >= 0 ; $opponent-- )
	if ( ! $teams[$opponent]->hasMatch && ! $teams[$bottom]->hasPlayed($teams[$opponent]->id, $context) ) //if this is a valid pairing
		{//create the match, then recurse to find complete set of pairings
		$teams[$bottom]->hasMatch = $teams[$opponent]->hasMatch = true;
		$match = new Match($teams[$bottom], $teams[$opponent]);
		$matches->push($match);

		if ( self::pairTopTeams($teams, $context, $matches))
			return $matches;
		else //could not find a valid set of fixtures for this pairing
			{
			$match = $matches->pop();
			$match->home->hasMatch = $match->away->hasMatch = false;
			}//end could not find a valid set of fixtures for this pairing
		}//end if this pair can play one another

return false;
}//end pair bottom teams
}//end class
?>
