<?php
include "init.php";

// JSONP
if ( isset( $_GET['c'] ) ) echo $_GET['c'].'(';

// what do they want?
if ( !isset( $_GET['q'] ) )
	$_GET['q'] = '';
switch ( $_GET['q'] ) {
	// number of servers
case 'servers':
	$sockcap = $settings['check-socket'] ? $settings['check-socket'] : 255;
	$q = $db->fetch_array( $db->simple_select(
			"acrms_servers", "COUNT(*) As n", "failures < $sockcap AND proto >= {$settings['minprotocol']}"
		) );
	// write output
	$json = array( 'active' => $q['n'], 'total' => $cache->read( "acrms_servs" ) );
	$json['hidden'] = $json['total'] - $json['active'];
	break;
	// unknown
default:
	$json = array( 'error' => 'invalid query' );
	break;
}

// write the JSON output
echo json_encode( $json ); // (object)

// JSONP
if ( isset( $_GET['c'] ) ) echo ')';
