<?php
include "init.php";

// FIXME: is this really the right time to do cron jobs?
$db->delete_query( "acrms_servers", "`time` < ".( time() - 3840 ) ); // give 64 minutes
if ( $db->affected_rows() ) {
	$q = $db->fetch_array( $db->simple_select( "acrms_servers", "COUNT(*) AS n" ) );
	$cache->update( "acrms_servs", (int)$q['n'] );
}
$db->delete_query( "acrms_auth", "`time` < ".( time() - 30 ) );

$ip = get_ip();
//$cdefs = isset($_GET['cdefs']) ? (int)($_GET['cdefs']) : 0;
//$guid = isset($_GET['guid']) ? (int)($_GET['guid']) : 0;

// Buffer up the lines
$lines = array();

// Show ban message
$banned = ip_in_list( $ip, $settings['bans'] );
// Master-Server flags
$msf = 0;
if ( ip_in_list( $ip, $settings['allows'] ) !== false ) {
	$msf |= 1;
	$banned = false;
}
elseif ( $banned !== false )
	$msf |= 2;
if ( ip_in_list( $ip, $settings['mutes'] ) !== false )
	$msf |= 4; // muted flag
$lines[] = "masterserver_flags $msf";

// Current version
if ( $_GET['act'] == 'version' || $_GET['act'] == 'update' ) {
	$lines[] = "current_version {$settings['currentgame']} {$settings['minprotocol']}";
	$lines[] = "echo \"\f2Welcome to the ACR Master Server!\"";
	if ( $banned !== false )
		$lines[] = "echo \"\fsNOTE: \f1you \f2are \f3banned \f0for \fr{$settings['bans'][$banned][2]}\"";
}

// Provide server list
if ( $_GET['act'] == 'list' || $_GET['act'] == 'update' ) {
	// total number
	$total = (int)$cache->read( "acrms_servs" );
	// displayed number
	$servs = 0;

	$sockcap = $settings['check-socket'] ? $settings['check-socket'] : 255;
	$q = $db->simple_select( "acrms_servers", "ip,port", "failures < $sockcap AND proto >= {$settings['minprotocol']}" );
	while ( $r = $db->fetch_array( $q ) ) {
		$host = $ip = $r['ip'];
		$port = $r['port'];
		// hostname subsitution
		if ( isset( $settings['translations'][$ip] ) )
			$host = $settings['translations'][$ip];
		// weights
		$wt = "";
		if ( isset( $settings['weights']["$ip:$port"] ) )
			$wt = " ".$settings['weights']["$ip:$port"];
		// add to output
		$lines[] = "addserver $host".( ( $wt || $port != 28770 ) ? " $port" : '' ).$wt;
		// count valid servers
		++$servs;
	}

	// use our placeholder if needed!
	//if(!$servs) $lines []= "addserver no-servers--please-run-one // placeholder";

	// Show hidden count
	$lines[] = "// ".( $total - $servs )." hidden";
}

// Write output
echo implode( "\n", $lines );
