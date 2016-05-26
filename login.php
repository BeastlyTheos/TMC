<?php
include "../PHT.php";
$key = "hrLVfkDKtEl9ZxXcp7jNr2";
$secret = "htARgp8rOVLEAvtLcVuxKLh3nTsNfU7qUOjjCWv5EhA"; 
$callBackURL = "http://localhost/tournament/save_tokens.php";
session_start();
try {
	$HT = new CHPPConnection($key, $secret, $callBackURL);
	$url = $HT->getAuthorizeUrl();
	}
catch(HTError $e)
	{echo $e->getMessage();}
$_SESSION['HT'] = $HT;
	header('Location: '.$url);
?>
