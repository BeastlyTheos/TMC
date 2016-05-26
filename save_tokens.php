<?php
$mysql_host = 'localhost';
$mysql_user = "root";
$mysql_pw = "";
$db = 'tournament';
include "..\pht.php";
//include "local_variables.php";
Include "..\yoursql.php";
session_start();

/*check if user has access, if not, redirect to login page*/
/*first check   if pht connection is persisted*/
if(! isset($_SESSION["HT"]))
	{echo "no chpp connection is persisted.<br/>";
	exit;
	}

$HT = $_SESSION["HT"];
if(! (isset($_REQUEST["oauth_token"]) && isset($_REQUEST["oauth_verifier"])))
	{echo "no parameters in URL<br/>";
	exit;
	}
 $HT->retrieveAccessToken($_REQUEST['oauth_token'], $_REQUEST['oauth_verifier']);

echo "<title>signedIn</title>";
echo $HT->getTeam()->getTeamName()."<br/>".time()."<br/>";
echo $HT->getOauthToken().' '.$HT->getOauthTokenSecret()	;


//include "local_variables.php";
//include "yoursql.php";
//yoursql_query("create table users if not exists (userName varchar(50), OauthToken varchar(50), oauthTokenSecret varchar(50), updated int unsigned);", "creating table");
$query = "replace into matches.users values('".$HT->getTeam()->getTeamName()."', '".$HT->getOauthToken()."', '".$HT->getOauthTokenSecret()."'	, ".time().")	;";
yoursql_query($query, "updating user table");
if(mysql_errno())
	echo "failed to save tokens";
else
	echo "<br/>saved tokens successfully";
?>