<?php $debug = true;
require_once 'C:/xampp/vendor/autoload.php';
  $loader = new Twig_Loader_Filesystem('templates');
 $twig = new Twig_Environment($loader, array("debug"=>$debug));

include "TournamentFunctions.php";

$twig->display("index.html", array(
	"edition"=>$edition,
	"context"=>$context
	)
);
?>
