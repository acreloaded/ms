<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// lazy lazy lazy...
$name = 'Server Weights';
$table = 'servers_weights';
$type = array(
	'ip' => array( 'IP', 2 ),
	'port' => array( 'Port (0 for *)', 4 ),
	'weight' => array( 'Weight', 4 ),
);
$sort = 'ip ASC, port DESC, weight DESC';

$remarks = 'These weights affect the servers\' position on the serverlist.';

function toEntry( $row ) {
	if ( $row['port'] ) return long2ip( $row['ip'] ).":$row[port] @ $row[weight]";
	return long2ip( $row['ip'] )." @ $row[weight]";
}

function toDel( $row ) {
	return "$row[ip]-$row[port]";
}

function fromDel( $str ) {
	// unsanitized
	$v = explode( '-', $str );
	return "`ip`=" . intval( $v[0] ) . " AND `port`=" . intval( $v[1] );
}

include 'builder.php';
