<?php
if ( !defined( 'IN_ACRMS' ) ) die( 'Direct initialization of this file is not allowed.' );

// dependencies
require_once 'class_auth.php';

class authdata { // sort of a struct
	public $id, $name, $level;

	function __construct() { // default values
		$this->id = -1;
		$this->name = 'guest';
		$this->level = -1;
	}

	function asName( $uid ) {
		global $db;
		if ( ( $uid = intval( $uid ) ) == $this->id )
			return '<u>you</u>';
		elseif ( $q2 = $db->fetch( $db->select( 'users', 'usr', "id=$uid" ) ) )
			return $q2['usr'];
		else return '[nobody]';
	}
}

class auth {
	var $uid, $hash, $data;

	function __construct() {
		$this->checkauth();
	}

	function checkauth() {
		// sanitize
		$this->hash = auth_checks::pwd( auth_cookies::key() );
		if ( $this->hash != auth_cookies::key() )
			auth_cookies::set_key( $this->hash );
		// get uid/data
		list( $this->uid, $this->data ) = self::get_credentials( $this->hash );
	}

	function asName( $uid ) {
		return $this->data->asName( $uid );
	}

	function get_credentials( $key ) {
		if ( !auth_cookies::check() ) return array( -1, new authdata );
		global $db;

		$ip = ip::getlong();
		$q = $db->fetch( $db->select( 'users_sessions', 'uid', "`key`='$key' AND `ip`=$ip", '', 1 ) );
		if ( $q !== false ) {
			// keep-alive session
			$db->update( 'users_sessions', array( 'time' => time() ), "`key`='$key'" );
			$data = new authdata;
			$uid = $data->id = intval( $q['uid'] );
			// fetch name and level
			$q = $db->fetch( $db->select( 'users', 'usr,priv', "`id`=$uid" ) );
			if ( $q !== false ) { // should always be true anyways
				$data->name = $q['usr'];
				$data->level = $q['priv'];
				return array( $uid, $data );
			}
		}
		// no match
		$this->logout();
		return array( -1, new authdata );
	}

	static function privname( $p ) {
		$privs = array( -1 => 'guest', 'user', 'master', 'admin', 'owner' );
		return isset( $privs[$p] ) ? $privs[$p] : 'unknown';
	}

	function reaches( $priv ) {
		return $this->data->level >= $priv;
	}

	function authed() {
		return $this->reaches( 0 );
	}

	function login( $u, $p ) {
		$u = auth_checks::usr( $u );
		$p = auth_checks::pwd_plain( $p );
		global $db;
		$q = $db->fetch( $db->select( 'users', 'id', "`usr`='$u' AND `pwd`='$p'" ) );
		if ( $q !== false ) {
			auth_cookies::set_key( $this->hash = sha1( random::salt( 35 ) ) );
			// new session
			$db->insert( 'users_sessions', array(
					'key' => $this->hash,
					'uid' => $this->uid = $q['id'],
					'ip' => ip::getlong(),
					'time' => time(),
				) );
			$this->checkauth();
			return true;
		}
		$this->checkauth();
		return $this->authed();
	}

	function logout() {
		if ( !$this->authed() ) return false;
		global $db;
		// destroy session
		$db->delete( 'users_sessions', "`key`='{$this->hash}'" );
		auth_cookies::destroy();
		$this->checkauth();
		return true;
	}
}
