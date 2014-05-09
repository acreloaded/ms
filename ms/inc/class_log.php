<?php
if ( !defined( 'IN_ACRMS' ) ) die( 'Direct initialization of this file is not allowed.' );

abstract class LogFlush {
	abstract public static function input( array $flushes, $level, $time, $flushed );
}

abstract class Log {
	abstract protected static function send( $msg, $level );
	public function alert( $msg ) { return $this->send( $msg, 'ALERT' ); }
	public function auth( $msg ) { return $this->send( $msg, 'AUTH' ); }
	public function info( $msg ) { return $this->send( $msg, 'INFO' ); }

	public function logrequest( $msg, $method ) {
		$this->$method( $_SERVER['SERVER_PROTOCOL']." {$_SERVER['REQUEST_METHOD']} {$_SERVER['REQUEST_URI']}\n".$msg );
	}

	public function a( $msg ) { return $this->logrequest( $msg, 'alert' ); }
	public function aa( $msg ) { return $this->logrequest( $msg, 'auth' ); }
	public function i( $msg ) { return $this->logrequest( $msg, 'info' ); }

	abstract public function flush( LogFlush $f, $level = 'INFO' );
	abstract public static function flushed( array $ids );
}
