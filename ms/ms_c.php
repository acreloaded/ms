<?php
include 'init.php';

// FIXME: is this really the right time to do cron jobs?
$db->delete_query( 'acrms_servers', '`time` < '.( time() - 3840 ) ); // give 64 minutes
if ( $db->affected_rows() ) {
	$q = $db->fetch_array( $db->simple_select( 'acrms_servers', 'COUNT(*) AS n' ) );
	$cache->update( 'acrms_servs', (int)$q['n'] );
}
$db->delete_query( 'acrms_auth', '`time` < '.( time() - 30 ) );

$ip_raw = inet_pton( ip4to6( get_ip() ) );
// ignore $_GET['build_defs']
// ignore $_GET['guid32']

// Buffer up the lines
$lines = array();

// Show ban message
$banned = ip_in_list( $ip_raw, $settings['bans'] );
// Master-Server flags
$msf = 0;
if ( ip_in_list( $ip_raw, $settings['allows'] ) !== false ) {
	// whitelisted flag
	$msf |= 1;
	$banned = false;
}
elseif ( $banned !== false ) {
	// banned flag
	$msf |= 2;
}
if ( ip_in_list( $ip_raw, $settings['mutes'] ) !== false ) {
	// muted flag
	$msf |= 4;
}
$lines[] = "masterserver_flags $msf";

// Current version
$lines[] = "current_version {$settings['currentgame']} {$settings['curprotocol']}";

// MS messages
$lines[] = 'echo "'."\f".'2Welcome to the ACR Master Server!"';
if ( $banned !== false && isset($settings['bans'][$banned][2]) ) {
	$lines[] = "echo \"\fsNOTE: \f1you \f2are \f3banned \f0for \fr{$settings['bans'][$banned][2]}\"";
}

// Provide server list
/*if ( true )*/ {
	// TODO: remove server count (it just wastes time and storage?)
	// total number
	$total = (int)$cache->read( 'acrms_servs' );
	// displayed number
	$servs = 0;

	$sockcap = $settings['check-socket'] ? $settings['check-socket'] : 255;
	$q = $db->simple_select( 'acrms_servers', 'ip,port', "failures < $sockcap AND proto >= {$settings['minprotocol']}" );
	while ( $r = $db->fetch_array( $q ) ) {
		$host = $ip = $r['ip'];
		$port = $r['port'];

		// Substitute hostname
		if ( isset( $settings['translations'][$ip] ) )
			$host = $settings['translations'][$ip];

		// Override weight
		$wt = '';
		if ( isset( $settings['weights']["$ip:$port"] ) )
			$wt = ' '.$settings['weights']["$ip:$port"];

		// Add server line
		$lines[] = "addserver $host".( ( $wt || $port != 28770 ) ? " $port" : '' ).$wt;
		++$servs;
	}

	/*
	// Use a placeholder if needed!
	if(!$servs) {
		$lines []= 'addserver no-servers--please-run-one // placeholder';
	}
	*/

	// Show hidden count
	$lines[] = '// '.( $total - $servs ).' hidden';
}

// Write output
echo implode( "\n", $lines );
