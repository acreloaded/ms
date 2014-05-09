<?php
/**
 * Victor's MS Crons
 */
if ( !defined( 'IN_ACRMS' ) ) die( 'Direct initialization of this file is not allowed.' );

class cron {
	static function run() {
		global $db, $cache;
		$db->delete( 'servers', '`time` < '.( time() - 3840 ) ); // give 64 minutes
		if ( $db->effect() > 0 ) {
			$q = $db->fetch( $db->select( 'servers', 'COUNT(*) AS n' ) );
			$cache->put( 'servs', (int)$q['n'] );
		}
		$db->delete( 'auth', '`time` < '.( time() - 30 ) ); // authentication requests expire in half a minute
		//$db->delete('authtime', '`time` < '.(time() - 10)); // forget servers that requested authentication after 10 seconds
		$db->delete( 'users_sessions', '`time` < '.( time() - 3600 ) ); // prune stale panel sessions
	}
}
