<?php $debug = true;
require_once 'C:/xampp/vendor/autoload.php';
  $loader = new Twig_Loader_Filesystem('templates');
 $twig = new Twig_Environment($loader, array("debug"=>$debug));

include "yoursql.php";
include "TournamentFunctions.php";
include "match.php";

$res = yoursql_query("select * from matches where context = $context");
$matches = [];
while ($data=$res->fetch())
	$matches[] = new Match($data);

$twig->display("view_matches.html", array(
	"matches"=>$matches,
	)
);
?>
