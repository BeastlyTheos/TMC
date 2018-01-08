<?php
class pairing_state_machine
{
public static function create($teams)
{
if ( count($teams) %2 == 1 )
	 {
	 //find fewest number of byes recieved by a team that has played the maximum number of games
	 $maxPlayed = 0;
	 $maxByes = 0;
	 foreach ( $teams as $team )
		 {
		 $maxByes = $team->byes > $maxByes?  $team->byes:  $maxByes;
		 $maxPlayed = $team->gamesPlayed > $maxPlayed?  $team->gamesPlayed:  $maxPlayed;
		 }

	for ( $gp = $maxPlayed ;  $gp >= 0 ; $gp--)
		for ( $byes = 0 ; $byes <= $maxByes ; $byes++ )
			{//find the lowest-ranked team with the above number of byes and games played
			for ( $i = count($teams) -1 ;  $i >= 0 ; $i--)
				if ( $teams[$i]->gamesPlayed == $gp && $teams[$i]->byes == $byes )
					echo "<p>{$teams[$i]->name}</p>";
			}//end iterating through teams with $gp games played and $numByes byes
	}//end if there is an odd number of teams
else //there are not an odd number of teams
	 echo "<p>there are an even number of teams</p>";
}//end function create
}
?>
