<?php
if ( !defined( 'IN_ACRMS' ) ) die( 'Direct initialization of this file is not allowed.' );

class LogDB extends Log{

	const FLUSHINTERVAL = 3600;
	const FLUSHFORCED = false; // if interval is increased

	protected static function send( $msg, $level ) {
		global $db;

		$db->insert( 'log', array(
				'time' => date( 'Y-m-d h:i:s' ),
				'content' => $db->escape( $msg ),
				'level' => $db->escape( $level ),
				'issue' => floor( time()/self::FLUSHINTERVAL ),
			) );
	}

	public function flush( LogFlush $f, $level = 'INFO', $forced = false ) {
		global $db;

		$current_issue = floor( time()/self::FLUSHINTERVAL );
		if ( self::FLUSHFORCED ) $forced = true;
		$q = $db->select( 'log', 'id,time,content,issue', ( $forced ? '' : "issue<$current_issue AND " )."level='".$db->escape( $level )."'", 'id ASC' );
		$issues = array();
		while ( $r = $db->fetch( $q ) )
			$issues[$r['issue']][] = array(
				'['.date( 'Y-m-d h:i:s', strtotime( $r['time'] ) ).'] '.$r['content'],
				$r['id'],
			);
		foreach ( $issues as $i => $issue )
			$f->input( $issue, $level, $i*self::FLUSHINTERVAL, array( $this, 'flushed' ) );
	}

	public static function flushed( array $ids ) {
		if ( !count( $ids ) ) return;
		global $db;

		$db->delete( 'log', '`id` IN ('.implode( ',', $ids ).')' );
	}
}
