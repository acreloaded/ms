<?php

define( 'IN_ACRMS', 1 );
require_once 'global.php';
require_once ACRMS_ROOT.'/inc/class_bans.php';

$ip = ip::getlong();
$port = intval( $_GET['port'] );
$proto = intval( $_GET['proto'] );
$guid = intval( $_GET['guid'] );

// are we open?
if ( !$config['servers']['autoapprove'] ) exit( 'automatic registration is closed.' );

// check bans...
if ( bans::matchIP( $ip ) ) exit( 'IP is blacklisted' );

// check socket?
if ( $config['servers']['check-socket'] || $config['servers']['check-socket-force'] ) {
	// sockets pwn!
	// noob socket 101
	$sip = ip::get(); // IP as string
	/*
	$fsock = fsockopen("udp://$sip", $port, $errno, $errstr, 2);
	//if(!$fsock) nosock($port); // lazy test doesn't always catch it
	fclose($fsock);
	*/
	$fsock = @fsockopen( "udp://$sip", $port + 1, $errno, $errstr, 3 );
	//if(!$fsock) nosock($port);
	stream_set_timeout( $fsock, 3 );
	fwrite( $fsock, '1' ); // standard ping: data[0] != NULL

	if ( fread( $fsock, 1 ) ) $sock = true; // if anything comes back...
	else $sock = false; // fail...

	fclose( $fsock );
} else $sock = true; // bypass it...

// are we renewing?
$renew = $db->fetch( $db->select( 'servers', 'failures', "`ip`=$ip AND `port`=$port" ) );
if ( $renew ) { // renew the server
	$sockn = ( ( $sock || $config['servers']['check-socket-force'] ) ? 0 : $renew['failures'] + 1 );
	if ( $sockn != 255 && $sockn > $config['servers']['check-socket'] )
		$sockn = $config['servers']['check-socket'];
	$db->update( 'servers', array(
			'time' => time(),
			'proto' => $proto,
			'failures' => $sock,
		), "`ip`=$ip AND `port`=$port" );
} else { // register it
	$cache->put( 'servs', $cache->get( 'servs' ) + 1 );
	$db->insert( 'servers', array(
			'ip' => $ip,
			'port' => $port,
			'time' => time(),
			'proto' => $proto,
			'failures' => ( $sockn = ( ( $sock || $config['servers']['check-socket-force'] ) ? 0 : 255 ) ),
		) );
}

// check for errors...
$error = false;
// check protocol
//if($proto < $settings['minprotocol']) $error = "!!! UPDATE !!! Minimum protocol is {$settings['minprotocol']}";
if ( $proto < $settings['minprotocol'] ) $error = '!!! UPDATE !!! There is a new version required!';
// check port
elseif ( $port < $settings['minport'] ) $error = "port less than {$settings['minport']}";
elseif ( $port > $settings['maxport'] ) $error = "port more than {$settings['maxport']}";
// check socket result
elseif ( $config['servers']['check-socket-force'] ); // empty
elseif ( $sockn == 255 ) $error = "unreachable (UDP $port/".( $port + 1 ).") on first attempt";
elseif ( $sockn && $sockn >= $config['servers']['check-socket'] ) $error = "unreachable (UDP $port/".( $port + 1 ).") $sockn/{$config['servers']['check-socket']} warnings exceeded";

// output the final answer
$act = 're'.( $renew ? 'new' : 'gister' ).'ed';
if ( $error !== false ) $msg = "ERROR: $error - server not $act";
elseif ( $sockn ) $msg = "server $act -- WARNING $sockn/{$config['servers']['check-socket']} unreachable (UDP $port/".( $port + 1 ).")";
else $msg = "server $act".( ( $renew || $config['servers']['check-socket'] || ( $config['servers']['check-socket-force'] && $sock ) ) ? '' : " -- (no error) reminder to check port-forward/firewall" );
echo $msg;

// use that same $msg for logging
$msg .= "
Host/Port: ".ip::get().":$port ($ip)
User-Agent: {$_SERVER['HTTP_USER_AGENT']}
GUID: ".sprintf( '%08X', $guid )." ($guid)";
$log->i( $msg );
