<?php

define( 'IN_ACRMS', 1 );
require_once 'global.php';
require_once ACRMS_ROOT.'/inc/class_auth.php';
require_once ACRMS_ROOT.'/inc/class_bans.php';

$ip = ip::getlong();
//$port = intval($_GET['port']);

$cip = sprintf( '%u', intval( $_GET['ip'] ) );
$guid = intval( $_GET['guid'] ); // TODO: use -- make alerts

// check bans...
$verdict = '*a';
$log_msg = 'allowed';
switch ( bans::matchAll( $cip ) ) {
	// IP blacklisted
case 1: $verdict = '*bi'; $log_msg = 'IP blacklisted'; break;
	// Muted
case 2: $verdict = '*bm'; $log_msg = 'IP muted'; break;
	// IP whitelisted
case -1: $verdict = '*bw'; $log_msg = 'IP whitelisted'; break;
	// unlisted...
}

echo $verdict;
$msg = "client connected to server ($log_msg)
IP (client -> server): ".long2ip( $cip )." ($cip) -> ".ip::get()." ($ip)
Server User-Agent: {$_SERVER['HTTP_USER_AGENT']}
GUID of client: ".sprintf( '%08X', $guid )." ($guid)";
$log->aa( $msg );
