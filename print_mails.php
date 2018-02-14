 <title>Print Mails</title><?php
include "CHPPConnection.php";

$res = yoursql_query('select name, userId from teams where ! isBot group by userId order by id asc');
echo "mails:<br/>\n";
while($r = $res->fetch_assoc())
	{//$club = $HT->getTeam($r['id']);
	echo '<a target="_blank" href="'.GetClubURL($r).'">'.$r["name"].'</a><br/>';
	//echo '<a target="_blank" href="'.GetMailURL($club->getLoginName()).'">'.$club->getTeamName().'</a><br/>\n';
	}


function getClubURL( $team)
{
return "http://hattrick.org/goto.ashx?path=/MyHattrick/Inbox/?actionType=newMail%26userId={$team['userId']}";

}
?>
