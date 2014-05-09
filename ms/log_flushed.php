<?php
if ( $_GET['key'] != 'noob4gy34098gj034j09j09909949_R_(J_(_J34jj-34j-g-o34:;' ) exit( 'nope.' );

define( 'IN_ACRMS', 1 );
require_once 'global.php';

$ids = preg_replace( '/[^0-9,]+/', '', $_GET['ids'] );
$log->flushed( explode( ',', $ids ) );

echo 'deleted $ids';
