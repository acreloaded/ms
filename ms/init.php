<?php
// Load MyBB global scripts
define( 'IN_MYBB', 1 );
define( 'NO_ONLINE', 1 );
require_once '../global.php';

// Report errors
error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

// Disable caching
header( 'Last-Modified: ' . gmdate( DATE_RFC2822 ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Check for installation
if ( !$db->table_exists( 'acrms_servers' ) ) {
	die( '// Error: ACRMS is not installed and activated.' );
}

// Global settings
$settings['autoapprove'] = true; // servers can be registered
$settings['check-socket'] = 0; // check server with UDP sockets or not (maximum failures or 0)
$settings['check-socket-force'] = false; // force a check if the above is 0

$settings['minprotocol'] = 138; // enforced
$settings['currentgame'] = 10000; // display only

// IPv6 lists
// array(left, right[, reason]),
$settings['allows'] = array(
);
$settings['bans'] = array(
);
$settings['bans_server'] = array(
);
$settings['mutes'] = array(
);

function ip_in_range( $ip_raw, $range ) {
	$l = inet_pton( $range[0] );
	$r = inet_pton( $range[1] );
	return $l <= $ip_raw && $ip_raw <= $r; // && strlen( $l ) == strlen( $ip_raw ) && strlen($r) == strlen($ip_raw)
}

function ip_in_list( $addr, $list ) {
	$addr_raw = inet_pton( $addr );

	foreach ( $list as $k => $range )
		if ( ip_in_range( $addr_raw, $range ) )
			return $k;

    return false;
}

// Serverlist settings
$settings['translations'] = array(
	// ip => domain
	'159.203.33.125' => 'play.acr.victorz.ca',
);
$settings['weights'] = array(
	// ip:port => weight
	'159.203.33.125:28770' => 1337, // 1000 is enough
);
