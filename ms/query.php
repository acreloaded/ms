<?php
include "init.php";

// JSONP
if ( isset( $_GET['c'] ) ) echo $_GET['c'].'(';

// what do they want?
if ( !isset( $_GET['q'] ) )
	$_GET['q'] = '';
switch ( $_GET['q'] ) {
case 'servers':
	// number of servers
	$sockcap = $settings['check-socket'] ? $settings['check-socket'] : 255;
	$q = $db->fetch_array( $db->simple_select(
			"acrms_servers", "COUNT(*) As n", "failures < $sockcap AND proto >= {$settings['minprotocol']}"
		) );
	// write output
	$json = array( 'active' => $q['n'], 'total' => $cache->read( "acrms_servs" ) );
	$json['hidden'] = $json['total'] - $json['active'];
	break;

case 'json':
	// server list
	$sockcap = $settings['check-socket'] ? $settings['check-socket'] : 255;
	$q = $db->simple_select( "acrms_servers", "ip,port", "failures < $sockcap AND proto >= {$settings['minprotocol']}" );
	$servers = array();
	while ( $r = $db->fetch_array( $q ) ) {
		$host = $r['ip'];
		$port = $r['port'];
		// hostname subsitution
		if ( isset( $settings['translations'][$host] ) )
			$host = $settings['translations'][$host];
		// add to list
		$servers[] = array( $host, (int)$port );
	}
	$json = array( 'servers' => $servers );
	break;
default:
	// unknown
	$json = array( 'error' => 'invalid query' );
	break;
}

// write the JSON output
echo json_encode( $json ); // (object)

// JSONP
if ( isset( $_GET['c'] ) ) echo ')';
