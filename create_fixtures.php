<?php $debug = true;
require_once 'C:/xampp/vendor/autoload.php';
  $loader = new Twig_Loader_Filesystem('templates');
 $twig = new Twig_Environment($loader, array("debug"=>$debug));

include 'CHPPConnection.php';
include 'TournamentFunctions.php';
include 'team.php';
include 'match.php';
$upcomingRound  = CurrentRound;
$context = $_GET['c'];

        try
        {
$res = yoursql_query("call getStandings($context)");
$numTeams = $res->num_rows;
$maxgp = 0;
$matches = new SplStack();

//load the teams from the sql result into the teams array
for ($i = 0; $i < $numTeams; $i++)
	{$r = $res->fetch_assoc();
	$teams[$i] = new Team($r['id'], $i, $r['name'], $r['gp']);
	if ( $r['gp'] > $maxgp ) 
		$maxgp = $r['gp'];
	//if( $numTeams %2 && ($numTeams-1) /2 == $i)
		//{$i++;
		//$teams[$i] = new Team(0, $i, "bye");
		//$numTeams++;
		//}//end if this is the time to insert th bye
	}//end for loop
            if ($numTeams % 2 == 1) //insure there is an even number
	{$teams[$numTeams] = new Team(0, $numTeams, "bye");
	          $numTeams++;
	          }

//pair the $teams
            $current = 0; $opponent = 1;
            while ($matches->count() * 2 < $numTeams) //while there are still matches to be set
	{///printf("%d matches set.<br/> Starting with %s, played %d<br>", $matches->count(), $teams[$current]->name, $teams[ $current]->gamesPlayed);

	                //                find next $opponent for $current
	                while ($teams[$opponent]->hasMatch || $current == $opponent || $teams[$current]->hasPlayed($teams[$opponent]->id, $context) || (( ! $teams[$opponent]->id  || ! $teams[ $current]->id) && ($teams[$opponent]->gamesPlayed < $maxgp && $teams[$current]->gamesPlayed < $maxgp))) //while $current and $opponent are an invalid pairing
		                {///printf("Cannot play " . $teams[$opponent]->name.''. $teams[ $opponent]->gamesPlayed);
		///printf("%shasmatch, %ssame, %shasplayed<br/>", $teams[$opponent]->hasMatch, $current == $opponent, $teams[$current]->hasPlayed($teams[$opponent]->id, $context));
		$opponent++;
		                    if($opponent >= $numTeams) //while there's no more $opponents
			{//undo matches 
			$m = $matches->Pop();
	///printf('undid matche between %s and %s<br/>', $m->home->name, $m->away->name);
			$teams[$m->home->rank]->hasMatch = false; 
			$teams[$m->away->rank]->hasMatch = false;
			$current = $m->home->rank; $opponent = $m->away->rank; //set current and opponent to indicies of the popped match
			                        $opponent++; //increment opponent to insure this current is not paired against the same team again
			}//end if there's no more $opponents
		}//end while $current and $opponent are invalid pairing
		
//record current and opponent as a pairing
	///printf("Paired %s with %s<br/>", $teams[$current]->name, $teams[$opponent]->name);
	$matches->Push(new Match($teams[$current], $teams[$opponent]));
	
//find the next team needing a match
	while ($current < $numTeams && $teams[$current]->hasMatch)
		$current++; //increment  current to the next team without a match
	$opponent = $current + 1;
	}//end setting all $matches
            
            //load $matches into sql
            //first, swap the order of the stack
$temp = new SplStack();
            while (0 != $matches->count()) 
                $temp->Push($matches->Pop()); //reverse the order of the $matches
             $matches = $temp; 

$temp = new SplQueue(); //for holding byes

                        while ($matches->count() != 0)
            {
                $m = $matches->Pop();

                if (0 == $m->home->id)
	{$m->home->id = $m->away->id;
	$m->away->id = 0;
	}
                if (0 == $m->away->id)
                $temp->enqueue( $m);
                                else
                {//$r = yoursql_query(sprintf("select arenas.id from arenas inner join regions on arenas.region = regions.id where league = 17 and isBot and region not in (select region from prior_teams where id in (%d, %d))", $m->home->id, $m->away->id))->fetch_row();
                    $arenaID =  1; //$r[0];
                    yoursql_query('call ScheduleMatch('.$m->home->id.', '.$m->away->id.", $context, $upcomingRound)");
                }//end if not a bye
              }//END EACH MATCH
              while ( $temp->count() > 0)
              {$m = $temp->dequeue();
                                  yoursql_query("call scheduleBye( " . $m->home->id . ", $context, $upcomingRound)");
                                  }

                        ///printf("[/TABLE]");

$twig->display("create_fixtures.html", array("teams"=>$teams));
}//end try
        catch (HTError $e)
        { printf($e.GetType() + "<br/>" + $e.Message +"<br/>" + $e.StackTrace); }
?>