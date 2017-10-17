<?$today = new DateTimeImmutable();
$startOfNationalCup = new DateTimeImmutable("2017-08-24"); //thursday  before first national cup match
define("CurrentRound", CupRoundOf($today));

function CupRoundOf($date)
{global $today, $startOfNationalCup;
return (int) ($date->diff($startOfNationalCup)->days /7 +1);
}//end HTRound

/*function HTClubForumLink($id)
{return '[teamid='.$id.']';}
*/

function PrintSQL($query)
{$res = yoursql_query($query);
echo "<table>";
for($i = 0 ; $i < $res->field_count ; $i++)
	echo "<th>".$res->fetch_field_direct($i)->name."</th>";
echo '</tr>';
while($r = $res->fetch_row())
	{echo '<tr>';
	for($i = 0 ; $i < $res->field_count ; $i++)
		echo "<td>".$r[$i]."</td>";
	echo '</tr>';
	}
echo '</table>';
}

/*function FindTeamRegions()
{global $HT;
if( null != $res = yoursql_query("select id from prior_teams where ! regionId or !regionName"))
	{while($r = $res->fetch_assoc())
		{$team = $HT->getTeam($r["id"]);
		$regionId = $team->getRegionId();
		$regionName = $team->getRegionName();
		yoursql_query("update prior_teams set regionName = '$regionName', regionId = $regionId where id = ".$r['id']);
		
		}//end while there's another team
	}//end if there was team without region
else
	echo "all teams have a region";
}//end find team regions
*/

/*function FindArenaIds()
{global $HT;
//if( null != $res = yoursql_query("select team from arenas where ! id or id is null or id = 0 limit 10,10000"))
if( null != $res = yoursql_query("select a.team from arenas as a, arenas as b where a.team != b.team and a.id = b.id  limit 10,10000"))
	{while($r = $res->fetch_assoc())
		{$team = $HT->getTeam($r['team']);
		$arenaId = $team->getArenaId();
		$regionId = $team->getRegionId();
		$isBot = $team->isBot()? 1:0;
		yoursql_query("replace into arenas (id, region, isBot, team) values ($arenaId, $regionId, $isBot, ".$r['team'].")");
		
		}//end while there's another arena
	}//end if there was arena without id
else
	echo "all arenas have an id";
}//end find arena ids
*/


?>