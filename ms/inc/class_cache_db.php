<?php
/**
 * Victor's Database Cache
 */
if ( !defined( 'IN_ACRMS' ) ) die( 'Direct initialization of this file is not allowed.' );

class cache_db {
	// store stuff
	protected $storage;

	function __construct() {
		// this is where shit is stored!
		$this->storage = array();
	}

	// following functions expect global $db to be ready!
	function get( $key ) {
		global $db;
		if ( array_key_exists( $key, $this->storage ) ) return $this->storage[$key];
		$q = $db->fetch( $db->select( 'cache', '`val`', "`key`='".$db->escape( $key )."'" ) );
		return $this->storage[$key] = unserialize( $q['val'] );
	}

	function put( $key, $val ) {
		global $db;
		$db->replace( 'cache', array(
				'key'=> $db->escape( $key ),
				'val' => $db->escape( serialize( $this->storage[$key] = $val ) )
			)
		);
	}
}
