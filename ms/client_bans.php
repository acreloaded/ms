<?php

define( 'IN_ACRMS', 1 );
require_once 'global.php';
//require_once ACRMS_ROOT.'/inc/class_bans.php';

$lines = array( 'List of IP Bans/Allows' );

$q = $db->select( 'bans_ip', 'ipl,ipr,reason' );
$lines[] = 'Banned';
while ( $r = $db->fetch( $q ) ) {
	$msg = long2ip( $r['ipl'] );
	if ( $r['ipl'] != $r['ipr'] ) $msg .= ' to '.long2ip( $r['ipl'] );
	$msg .= ' for: '.$r['reason'];
	$lines[] = $msg;
}

$lines[] = '';

$q = $db->select( 'allows_ip', 'ipl,ipr,reason' );
$lines[] = 'Allowed';
while ( $r = $db->fetch( $q ) ) {
	$msg = long2ip( $r['ipl'] );
	if ( $r['ipl'] != $r['ipr'] ) $msg .= ' to '.long2ip( $r['ipl'] );
	$msg .= ' for: '.$r['reason'];
	$lines[] = $msg;
}

echo implode( "\n", $lines );
