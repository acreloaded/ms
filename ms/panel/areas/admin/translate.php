<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// lazy lazy lazy...
$name = 'Server Translations';
$table = 'servers_trans';
$type = array(
	'ip' => array( 'IP', 2 ),
	'domain' => array( 'Domain', 1 ),
	'port' => array( 'Port (0 for *)', 4 ),
);
$sort = 'ip ASC, port DESC, domain ASC';

$remarks = 'These entries convert IPs to domains on the serverlist. Specific ports take precedence over general ports.';

function toEntry( $row ) {
	if ( $row['port'] ) return long2ip( $row['ip'] ).":$row[port] -> $row[domain]";
	return long2ip( $row['ip'] )." -> $row[domain]";
}

function toDel( $row ) {
	return "$row[ip]-$row[domain]";
}

function fromDel( $str ) {
	// unsanitized
	$v = explode( '-', $str );
	global $db;
	return '`ip`=' . intval( $v[0] ) . " AND `domain`='" . $db->escape( $v[1] ) . "'";
}

include 'builder.php';
