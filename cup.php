<title>Cup Leavers</title>
<?php
include 'CHPPConnection.php';
$outOfCup = array();      

$res = yoursql_query("select id from standings where inNationalCup");

if($res)
	{echo "found ".$res->num_rows." teams<br/>";
	
	while($r = $res->fetch_assoc())
                    if( ! $HT->getTeam($r['id'])->isInCup())
		                    {$outOfCup[] = $r['id'];
	echo $HT->getTeam($r['id'])->getTeamName()." was eliminated.<br/>";
}
else
	echo $HT->getTeam($r['id'])->getTeamName()." is still in it.<br/>";


echo count($outOfCup)." teams left the cup";
	if(count($outOfCup)) //if there was a team that exited the cup
		yoursql_query("update standings set inNationalCup = 0 where id in (".implode(", ", $outOfCup).")");
	}//end if there are teams
else
	echo "no teams were in the cup to begin with";
?>