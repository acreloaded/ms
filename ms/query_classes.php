<?php

abstract class MsQuery {
	function output() {
		if ( $_GET['c'] ) echo $_GET['c'].'(';

		// stupid hack to get protected members (DOESN'T WORK WITH PRIVATE)
		$json = array();
		foreach ( $this as $key => $value )
			$json[$key] = $value;
		echo json_encode( (object)$json, JSON_FORCE_OBJECT );

		if ( $_GET['c'] ) echo ')';
	}
}

class MsQueryError extends MsQuery
{
	protected $error = 'invalid query';
}

class MsQueryServerNum extends MsQuery
{
	protected $active = 0;
	protected $hidden = 0;
	protected $total = 0;

	public function __construct() {
		// globals
		global $config, $db, $settings, $cache;
		// gather data...
		$sockcap = $config['servers']['check-socket'] ? $config['servers']['check-socket'] : 255;
		$q = $db->fetch( $db->select( 'servers', 'COUNT(*) As n', "failures < $sockcap AND
				ABS(proto) >= {$settings['minprotocol']} AND
				port >= {$settings['minport']} AND
				port <= {$settings['maxport']}" ) );
		// output
		$this->active = $q['n'];
		$this->total = $cache->get( 'servs' );
		$this->hidden = $this->total - $this->active;
	}
}
