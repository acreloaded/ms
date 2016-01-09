<?php
include "init.php";

$lines = array( 'List of IP Bans/Allows' );

$ip = get_ip();
$ip_raw = inet_pton( $ip );
$lines[] = "Your IP is $ip";

function output_list( $title, $list ) {
	global $lines, $ip, $ip_raw;
	$lines[] = $title;
	foreach ( $list as $entry ) {
		$msg = '';
		if ( ip_in_range( $ip_raw, $entry[0], $entry[1] ) )
			$msg = '>>> MATCH >>> ';
		$msg .= $entry[0];
		if ( $entry[0] != $entry[1] ) $msg .= ' to '.long2ip( $entry[1] );
		$msg .= ' ('.$entry[2].')';
		$lines[] = $msg;
	}
}

output_list( 'Banned hosts:', $settings['bans'] );
output_list( 'Allowed hosts:', $settings['allows'] );
output_list( 'Muted hosts:', $settings['mutes'] );

echo implode( "\n<br>", $lines );
