<?php
// Load MyBB global scripts
define("IN_MYBB", 1);
define("NO_ONLINE", 1);
require_once "../forum/global.php";

// Report errors
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Disable caching
header("Last-Modified: " . gmdate(DATE_RFC2822) . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check for installation
if(!$db->table_exists("acrms_servers"))
	die("// Error: ACRMS is not installed and activated.");

// Global settings
$settings['autoapprove'] = true; // servers can be registered
$settings['check-socket'] = 0; // check server with UDP sockets or not (maximum failures)
$settings['check-socket-force'] = false; // force a check if the above is 0

$settings['minprotocol'] = 100; // enforced
$settings['currentgame'] = 20600; // display only

// IP lists
	// array(left, right[, reason]),
$settings['allows'] = array(
);
$settings['bans'] = array(
);
$settings['bans_server'] = array(
);
$settings['mutes'] = array(
);

function ip_in_range($ip_raw, $l, $r) {
	$l = inet_pton($l);
	$r = inet_pton($r);
	return $l <= $ip_raw && $ip_raw <= $r && strlen($l) == strlen($ip_raw); // && strlen($r) == strlen($ip_raw)
}

function ip_in_list($addr, $list) {
	$addr_raw = inet_pton($addr);
	foreach($list as $k => $range)
		if(ip_in_range($addr_raw, $range[0], $range[1]))
			return $k;
	return false;
}

// Serverlist settings
$settings['translations'] = array(
	// ip => domain
	"173.224.216.229" => "play.acr.victorz.ca",
);
$settings['weights'] = array(
	// ip:port => weight
	"173.224.216.229:28770" => 1000,
);
