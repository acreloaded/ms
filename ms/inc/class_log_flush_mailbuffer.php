<?php
if ( !defined( 'IN_ACRMS' ) ) die( 'Direct initialization of this file is not allowed.' );

class LogFlushMailBuffer extends LogFlush{
	public static function input( array $flushes, $level, $time, $flushed ) {
		global $mailbuf;
		$obj = &$mailbuf[];
		$obj = array(
			'subject' => date( 'M d Y', $time )." $level"
		);

		$body = '';
		$flusharray = array();
		foreach ( $flushes as $flush ) {
			$body .= "$flush[0]\n\n";
			$flusharray[] = $flush[1];
		}

		$obj['body'] = $body;
		$obj['ids'] = implode( ',', $flusharray );

		// DEFER THIS CALL ELSEWHERE
		//call_user_func($flushed, $flusharray);
	}
}
