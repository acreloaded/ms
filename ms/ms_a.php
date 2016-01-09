<?php
include 'init.php';
require_once MYBB_ROOT.'inc/functions_user.php';

$cip_raw = isset( $_GET['a'] ) ? ip4to6( $_GET['a'] ) : '::1';
if ( !filter_var($cip_raw, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
	$cip_raw = '::1';
}
$cip_raw = inet_pton( $cip_raw );
// ignore $_GET['guid32']

$aid = isset( $_GET['i'] ) ? (int)( $_GET['i'] ) : 0;
$auser = isset( $_GET['u'] ) ? (int)( $_GET['u'] ) : 0;

// check bans...
$verdict = '*a';
if ( ip_in_list( $cip_raw, $settings['allows'] ) !== false )
	$verdict = "*bw"; // IP blacklisted
elseif ( ip_in_list( $cip_raw, $settings['bans'] ) !== false )
	$verdict = "*bi"; // IP whitelisted
elseif ( ip_in_list( $cip_raw, $settings['mutes'] ) !== false )
	$verdict = "*bm"; // Muted
// else // unlisted...

echo $verdict;

// Check auth request
if ( !$aid || !$auser )
	die();

echo "\n";

$ip = get_ip();
$port = isset( $_GET['p'] ) ? (int)( $_GET['p'] ) : 28770;

// are they unregistered?
$q = $db->fetch_array( $db->simple_select( "acrms_servers", "COUNT(*) AS n, authtime", "ip='$ip' AND port=$port" ) );
if ( !$q['n'] )
	die( "*f" ); // auth request fail - unregistered
// are they too busy?
if ( $q['authtime'] + 1 >= time() )
	die( "*f" ); // auth too busy

// does it already exist?
$q = $db->fetch_array( $db->simple_select( "acrms_auth", "COUNT(*) AS n", "ip='$ip' AND port=$port AND id=$aid" ) );
if ( $q['n'] )
	die( "*f" ); // auth id already exists

// update their timer
$db->update_query( "acrms_servers", array( "authtime" => time() ), "ip='$ip' AND port=$port" );

// is the user valid?
if ( !user_exists( $auser ) )
	die( "*f" ); // auth user not found

// do auth
$nonce = mt_rand( 0, 2147483647 ); // 31-bits because it's signed
$db->insert_query( "acrms_auth", array(
		"ip" => $ip,
		"port" => $port,
		"id" => $aid,
		"time" => time(),
		"nonce" => $nonce,
		"uid" => $auser,
	) );

echo "*c".$nonce;
