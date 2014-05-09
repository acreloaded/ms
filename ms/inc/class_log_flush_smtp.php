<?php
include 'Mail.php'; // PEAR needed

class LogFlushSMTP extends LogFlush {
	public static function input( array $flushes, $level, $time, $flushed ) {
		$recipients = $config['smtp']['to'];
		$headers = array (
			'From' => $config['smtp']['from'],
			'To' => join( ', ', $recipients ),
			'Subject' => date( 'M d Y', $time )." $level",
		);

		$body = '';
		$flusharray = array();
		foreach ( $flushes as $flush ) {
			$body .= "$flush[0]\n\n";
			$flusharray[] = $flush[1];
		}

		$mail_object =& Mail::factory( 'smtp',
			array(
				'host' => $config['smtp']['host'],
				'auth' => true,
				'username' => $config['smtp']['user'],
				'password' => $config['smtp']['pass'],
				//'debug' => true, # uncomment to enable debugging
			) );

		if ( $mail_object->send( $recipients, $headers, $body ) === true ) {
			call_user_func( $flushed, $flusharray );
		}
	}
}
