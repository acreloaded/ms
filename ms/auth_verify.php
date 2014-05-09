<?php

define( 'IN_ACRMS', 1 );
require_once 'global.php';
require_once ACRMS_ROOT.'/inc/class_auth.php';

$ip = ip::getlong();
$port = intval( $_GET['port'] );
$id = intval( $_GET['id'] );
$answer = auth_checks::pwd( $_GET['hash'] );

$msg = "
Host/Port (server): ".ip::get().":$port ($ip)
Answered Auth ID #$id with $answer";

// are they unregistered?
$q = $db->fetch( $db->select( 'servers', 'COUNT(*) AS n', "`ip`=$ip AND `port`=$port" ) );
if ( !$q['n'] ) { $log->aa( 'auth verify fail - unregistered'.$msg ); exit( '*f' ); }

// fetch it
//$q = $db->fetch($db->select('auth', '`hash`,`salt`,`uid`', "`ip`=$ip AND `port`=$port AND `id`=$id", '', 1));
$q = $db->fetch( $db->query( "SELECT a.hash,a.salt,u.usr,u.priv FROM {$config['db']['pref']}auth a INNER JOIN {$config['db']['pref']}users u ON a.uid=u.id" ) );

// does it NOT already exist?
if ( !$q ) { $log->aa( 'auth not found'.$msg ); exit( '*f' ); }

// do auth
// delete entry
$db->delete( 'auth', "`ip`=$ip AND `port`=$port AND `id`=$id" );

// final result
if ( sha1( md5( $answer, true ).sha1( $q['salt'], true ) ) == $q['hash'] ) {
	// match
	$log->aa( "auth pass as {$q['usr']} for level {$q['priv']}".$msg );
	echo "*s{$q['priv']}{$q['usr']}";
}
else {
	// no match
	$log->aa( 'auth mismatch'.$msg );
	echo '*d';
}
