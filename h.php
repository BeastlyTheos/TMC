<?php
include "CHPPConnection.php";
$matches = $HT->getYouthTeamArchiveMatches();
printf("There are %d  matches</br>", $matches->getMatchNumber());
for ($i =1 ; $i <= $matches->getMatchNumber() ; $i++) {
	$m = $matches->getMatch($i);
	echo "<p>".$m->getId()."</p>";
}
?>
