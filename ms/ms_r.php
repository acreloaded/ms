<?php
include 'init.php';

$ip = get_ip();
$port = isset( $_GET['p'] ) ? (int)( $_GET['p'] ) : 28770;
$proto = isset( $_GET['v'] ) ? (int)( $_GET['v'] ) : 0;
// ignore $_GET['guid32']

// are we open for business?
if ( !$settings['autoapprove'] )
	die( "automatic registration is closed" );

// check bans
if ( ip_in_list( inet_pton( ip4to6( $ip ) ), $settings['bans_server'] ) !== false ) {
	die( "ERROR: your IP is blacklisted" );
}

// check port
if ( $port < 0 ) {
	die( "ERROR: port must not be negative" );
} elseif ( $port >= 65535 ) {
	die( "ERROR: port must be under 65535" );
}

// check socket?
if ( $settings['check-socket'] || $settings['check-socket-force'] ) {
	// sockets pwn!
	// noob socket 101
	$fsock = @fsockopen( "udp://$ip", $port + 1, $errno, $errstr, 3 );
	//if(!$fsock) nosock($port);
	stream_set_timeout( $fsock, 3 );
	fwrite( $fsock, "1" ); // standard ping: any char not equal to the null byte

	if ( fread( $fsock, 1 ) ) $sock = true; // if anything comes back...
	else $sock = false; // fail...

	// clean up
	fclose( $fsock );
} else $sock = true; // bypass it...

// are we renewing?
$renew = $db->fetch_array( $db->simple_select( "acrms_servers", "failures", "ip='$ip' AND port=$port" ) );
if ( $renew ) {
	$failures = ( ( $sock || $settings['check-socket-force'] ) ? 0 : $renew['failures'] + 1 );
	if ( $failures != 255 && $failures > $settings['check-socket'] )
		$failures = $settings['check-socket'];
	$db->update_query( "acrms_servers", array(
			"time" => time(),
			"proto" => $proto,
			"failures" => $failures,
		), "ip='$ip' AND port=$port" );
} else { // register it
	$cache->update( "acrms_servs", (int)$cache->read( "acrms_servs" ) + 1 );
	$db->insert_query( "acrms_servers", array(
			"ip" => $ip,
			"port" => $port,
			"time" => time(),
			"proto" => $proto,
			"failures" => ( $failures = ( ( $sock || $settings['check-socket-force'] ) ? 0 : 255 ) ),
			"authtime" => 0,
		) );
}

// check for errors...
$error = false;
// check protocol
if ( $proto < $settings['minprotocol'] )
	$error = "!!! UPDATE !!! You must update to a newer version!";
// check socket result

// output the final answer
$act = $renew ? "renewed" : "registered";
if ( $error !== false ) {
	$msg = "ERROR: $error - server not $act";
} elseif ( $failures ) {
	$msg = "server $act -- WARNING $failures/{$settings['check-socket']} unreachable (UDP $port/".( $port + 1 ).")";
} else {
	$msg = "server $act";
	if ( $proto < $settings['curprotocol'] )
		$msg .= ' (!!! UPDATE !!! new version available)';
	if ( !( $renew || $settings['check-socket'] || ( $settings['check-socket-force'] && $sock ) ) )
		$msg .= " -- (no error) reminder to check port-forward/firewall";
}
echo $msg;
