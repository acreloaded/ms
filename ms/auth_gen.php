<?php

define( 'IN_ACRMS', 1 );
require_once 'global.php';
require_once ACRMS_ROOT.'/inc/class_auth.php';

$ip = ip::getlong();
$port = intval( $_GET['port'] );
$id = intval( $_GET['id'] );
$user = auth_checks::usr( $_GET['user'] );

$msg = "
Host/Port (server): ".ip::get().":$port ($ip)
Requested Auth ID #$id for $user";

// are they registered?
$q = $db->fetch( $db->select( 'servers', 'COUNT(*) AS n, `authtime`', "`ip`=$ip AND `port`=$port" ) );
// are they not?
if ( !$q['n'] ) { $log->aa( 'auth request fail - unregistered'.$msg ); exit( '*f' ); }
// are they too busy?
if ( $q['authtime'] + 1 >= time() ) { $log->aa( 'auth too busy'.$msg ); exit( '*f' ); }

// does it already exist?
$q = $db->fetch( $db->select( 'auth', 'COUNT(*) AS n', "`ip`=$ip AND `port`=$port AND `id`=$id" ) );
if ( $q['n'] ) { $log->aa( 'auth id already exists'.$msg ); exit( '*f' ); }

// update their timer
$db->update( 'servers', array( 'authtime' => time() ), "`ip`=$ip AND `port`=$port" );

// is the user valid?
$q = $db->fetch( $db->select( 'users', '`id`,`pwd`', "`usr`='$user'", '', 1 ) );
if ( !$q ) { $log->aa( 'auth user not found'.$msg ); exit( '*f' ); }

// do auth
$nonce = mt_rand( 0, 2147483647 ); // 31-bits signed
$answer = sha1( "{$user}:{$q['pwd']}!{$nonce}" );

// generate salt
$salt = random::salt( 25 );
// hash it
$hash = sha1( md5( $answer, true ).sha1( $salt, true ) );

$db->insert( 'auth', array(
		'ip' => $ip,
		'port' => $port,
		'id' => $id,
		'time' => time(),
		'hash' => $hash,
		'salt' => $salt,
		'uid' => $q['id'],
	) );
echo '*c'.$nonce;
$log->aa( "auth with nonce #$nonce".$msg );
