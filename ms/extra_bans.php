<?php
include 'init.php';

$lines = array( 'List of IP Bans/Allows' );

$ip = get_ip();
$ip_raw = inet_pton( $ip );
$lines[] = "Your IP is $ip";
$lines[] = '';

function output_list( $title, $list ) {
	global $lines, $ip_raw;

	// Header
	$lines[] = "$title hosts (".count($list).')';

	foreach ( $list as $entry ) {
		$msg = '';

		// Mark matched entries
		if ( ip_in_range( $ip_raw, $entry ) ) {
			$msg = '>>> MATCH >>> ';
		}

		// Range start
		$msg .= $entry[0];

		// Range end
		if ( $entry[0] != $entry[1] ) {
			$msg .= ' to '.long2ip( $entry[1] );
		}

		// Reason
		if ( isset($entry[2]) ) {
			$msg .= " ({$entry[2]})";
		}

		// Add line
		$lines[] = $msg;
	}
}

output_list( 'Banned', $settings['bans'] );
output_list( 'Allowed', $settings['allows'] );
output_list( 'Muted', $settings['mutes'] );

echo implode( "\n<br>", $lines );
