<?php
/**
 * Victor's ACR Ban Manager
 */
if ( !defined( 'IN_ACRMS' ) ) die( 'Direct initialization of this file is not allowed.' );

class bans {
	// is this IP banned?
	static function matchIP( $ip ) {
		return self::IPIsBanned( $ip ) && !self::IPIsAllowed( $ip );
	}

	/**
	 * is this user's IP/nickname banned?
	 * Returns:
	 * -1 - IP whitelisted
	 * 0 - Not listed
	 * 1 - IP blacklisted
	 * 2 - Muted by mute rules
	 */
	static function matchAll( $ip ) {
		if ( self::IPIsAllowed( $ip ) ) return -1;
		if ( self::IPIsBanned( $ip ) ) return 1;
		if ( self::isMuted( $ip ) ) return 2;
		return 0;
	}

	// is this IP muted?
	static function isMuted( $ip ) {
		// TODO
		return false;

		global $db;
		$q = $db->fetch( $db->select( 'mutes_ip', 'COUNT(*) as n', "`ipl` <= $ip AND `ipr` >= $ip" ) );
		$q = $q['n'];
		return $q > 0;
	}

	// is this ip blacklisted?
	static function IPIsBanned( $ip ) {
		global $db;
		$q = $db->fetch( $db->select( 'bans_ip', 'COUNT(*) as n', "`ipl` <= $ip AND `ipr` >= $ip" ) );
		$q = $q['n'];
		return $q > 0;
	}

	// is this ip whitelisted?
	static function IPIsAllowed( $ip ) {
		global $db;
		$q = $db->fetch( $db->select( 'allow_ip', 'COUNT(*) as n', "`ipl` <= $ip AND `ipr` >= $ip" ) );
		$q = $q['n'];
		return $q > 0;
	}
}
