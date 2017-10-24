<?php
include 'yoursql.php';
$context = $_GET['c'];
$contextName = yoursql_query("select name from contexts where id = $context")->fetch_row()[0];
echo "<title>standings for $context</title>";


$standings = yoursql_query("call getStandings($context)");

printf("[u]$contextName [/u]<br/>[table]<br/>[tr][th]Rank[/th][th]Team[/th][th]W[/th][th]L[/th][th]GF[/th][th]GA[/th][th]GR[/th][th]WP[/th][/tr]<br>\n");
for($i = 1 ; $r = $standings->fetch_assoc() ; $i++)
	{//if ( $i <= 3)
//	yoursql_query("call contextualise_team(".$r['id'].", 6, $i);");
$gr = sprintf("%.0f", $r['gr'] *1000);
	$wp = sprintf("%03.0f", $r['wp'] *1000);
	printf("[tr][td]%d[/td][td]%s[/td][td]%d[/td][td]%d[/td][td]%d[/td][td]%d[/td][td]%4s[/td][td]%4s[/td][/tr]<br>\n",
	$i, $r['name'], $r['w'], $r['l'], $r['gf'], $r['ga'], '.'.$gr, '.'.$wp);
	}
echo "[/table]";
?>