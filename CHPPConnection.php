<?php
include "yoursql.php";
include "pht.php";

$key = 'hrLVfkDKtEl9ZxXcp7jNr2';
$secret = 'htARgp8rOVLEAvtLcVuxKLh3nTsNfU7qUOjjCWv5EhA';
$HT = new CHPPConnection($key, $secret);

$res = yoursql_query("select oauthToken, oauthTokenSecret from matches.users;", "selecting token and secret");
$tokens = $res->fetch();
$HT->setOauthToken($tokens[0]);
//$HT->setOauthToken( 'POlWwPkzjqJiowBz');
$HT->setOauthTokenSecret($tokens[1]);
//$HT->setOauthTokenSecret( 'LyapIOWDNHavtDo4');

?>