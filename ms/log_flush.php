<?php
if ( $_GET['key'] != 'noob4gy34098gj034j09j09909949_R_(J_(_J34jj-34j-g-o34:;' ) exit( 'nope.' );

define( 'IN_ACRMS', 1 );
require_once 'global.php';
//require_once ACRMS_ROOT.'/inc/class_log_flush_smtp.php';
require_once ACRMS_ROOT.'/inc/class_log_flush_mailbuffer.php';

//$f = new LogFlushSMTP;
$mailbuf = array();
$f = new LogFlushMailBuffer;
$log->flush( $f, 'ALERT', true ); // ALERT
$log->flush( $f ); // INFO
$log->flush( $f, 'AUTH' ); // AUTH

echo json_encode( $mailbuf );
