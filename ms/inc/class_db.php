<?php
/**
 * Victor's MySQL library
 */
if ( !defined( 'IN_ACRMS' ) ) die( 'Direct initialization of this file is not allowed.' );

class DB_MySQL {
	protected $pref;
	private $access;

	function connect( $s, $u, $p, $db ) {
		$this->access = mysql_connect( $s, $u, $p );
		if ( $this->access ) mysql_select_db( $db, $this->access );
		global $config;
		$this->pref = $config['db']['pref'];
		return $this->access;
	}

	static function escape( $s ) {
		return mysql_real_escape_string( $s );
	}

	static function error() {
		return mysql_error();
	}

	function query( $q ) {
		return mysql_query( $q, $this->access );
	}

	function insert_id() {
		return mysql_insert_id( $this->access );
	}

	function effect() {
		return mysql_affected_rows( $this->access );
	}

	static function fetch( $r ) {
		if ( !$r ) return false;
		return mysql_fetch_assoc( $r );
	}

	function select( $table, $fields='*', $conditions='', $orderby='', $limit='' ) {
		$query = 'SELECT '.$fields." FROM {$this->pref}$table";
		if ( $conditions != '' )
			$query .= ' WHERE '.$conditions;
		if ( $orderby != '' )
			$query .= ' ORDER BY '.$orderby;
		if ( $limit != '' )
			$query .= ' LIMIT '.$limit;
		return $this->query( $query );
	}

	function insert( $table, $array ) {
		if ( !is_array( $array ) ) return false;
		$fields = '`'.implode( '`,`', array_keys( $array ) ).'`';
		$values = implode( "','", $array );
		$this->query( "INSERT INTO $this->pref$table (".$fields.") VALUES ('".$values."')" );
		return $this->insert_id();
	}

	function replace( $table, $array ) {
		if ( !is_array( $array ) ) return false;
		$fields = '`'.implode( '`,`', array_keys( $array ) ).'`';
		$values = implode( "','", $array );
		$this->query( "REPLACE INTO $this->pref$table (".$fields.") VALUES ('".$values."')" );
		return $this->insert_id();
	}

	function update( $table, $array, $where='', $limit='' ) {
		if ( !is_array( $array ) ) return false;
		$comma = '';
		$query = '';

		foreach ( $array as $field => $value ) {
			$query .= $comma.'`'.$field."`='{$value}'";
			$comma = ', ';
		}
		if ( $where != '' ) $query .= " WHERE $where";
		if ( $limit != '' ) $query .= " LIMIT $limit";
		return $this->query( "UPDATE $this->pref$table SET $query" );
	}

	function delete( $table, $where='', $limit='' ) {
		$query = '';
		if ( $where != '' ) $query .= " WHERE $where";
		if ( $limit != '' ) $query .= " LIMIT $limit";
		return $this->query( "DELETE FROM $this->pref$table$query" );
	}
}
