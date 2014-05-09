<?php

define( 'IN_ACRMS', 1 );
require_once 'global.php';
require_once ACRMS_ROOT.'/inc/class_bans.php';

$ip = ip::getint();
$cdefs = intval( $_GET['cdefs'] );
$guid = intval( $_GET['guid'] );

// master-server flags
$msf = 0;
if ( bans::IPIsAllowed( $ip ) )
	$msf |= 1;
else if ( bans::IPIsBanned( $ip ) )
		$msf |= 2;
	else if ( bans::isMuted( $ip ) )
			$msf |= 4; // muted flag
		$lines = array( "masterserver_flags $msf" );

	if ( $_GET['act'] == 'version' || $_GET['act'] == 'update' )
		$lines[] = "current_version {$settings['currentgame']} {$settings['minprotocol']}";

if ( $_GET['act'] == 'list' || $_GET['act'] == 'update' ) {
	// total number
	$total = $cache->get( 'servs' );
	// displayed number
	$servs = 0;

	$sockcap = $config['servers']['check-socket'] ? $config['servers']['check-socket'] : 255;
	$q = $db->select( 'servers', 'ip,port', "failures < $sockcap AND
					ABS(proto) >= {$settings['minprotocol']} AND
					port >= {$settings['minport']} AND
					port <= {$settings['maxport']}" );
	while ( $r = $db->fetch( $q ) ) {
		$ips = $r['ip'];
		$port = $r['port'];
		// hostname subsitution
		$host = $ips;
		// translations...
		$t = $db->select( 'servers_trans', 'domain', 'ip=$ips AND (port = 0 OR port = $port)', 'port DESC', 1 );
		if ( $t = $db->fetch( $t ) ) $host = $t['domain'];
		else $host = long2ip( $host );
		// weights
		$wt = false;
		$t = $db->select( 'servers_weights', 'weight', "ip=$ips AND (port = 0 OR port = $port)", "port DESC", 1 );
		if ( $t = $db->fetch( $t ) ) $wt = $t['weight'];
		// add to output
		$lines[] = "addserver $host".( ( $wt || $port != $settings['defaultport'] ) ? " $port $wt" : '' );
		// count valid servers
		++$servs;
	}

	// use our placeholder if needed!
	if ( !$servs ) $lines []= "addserver {$settings['placeholder']} // placeholder";

	$lines[] = '// '.( $total - $servs ).' hidden';
}

echo implode( "\n", $lines );

// logging
$ip_r = ip::get();
$flags = array();
switch ( $msf & 3 ) {
default: case 0: $flags[] = '(implicitly) allowed IP '; break;
case 1: $flags[] = '(explicitly) allowed IP'; break;
case 2: $flags[] = 'blocked IP'; break;
case 3: $flags[] = '(overriden) allowed IP'; break;
}
if ( $msf & 4 ) $flags[] = 'muted';
else $flags[] = 'not muted';
$flags = implode( ', ', $flags );
$cdefs_r = 'Unknown';
if ( $cdefs & 0x40 ) $cdefs_r = 'Windows';
elseif ( $cdefs & 0x20 ) $cdefs_r = 'Mac';
elseif ( $cdefs & 0x10 ) $cdefs_r = 'Linux';
if ( $cdefs & 0x02 ) $cdefs_r .= ', Debug';

$msg = "client requested serverlist, $servs/$total given
IP: $ip_r ($ip)
User-Agent: {$_SERVER['HTTP_USER_AGENT']}";
if ( $cdefs ) $msg .= "
Flags: $flags
Client Definitions: $cdefs [0x".sprintf( '%X', $cdefs )."] ($cdefs_r)";
if ( $guid ) $msg .= "
GUID: ".sprintf( '%08X', $guid )." ($guid)";

$log->i( $msg );
