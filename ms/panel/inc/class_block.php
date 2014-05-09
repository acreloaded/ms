<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed' );

abstract class PageBlock {
	abstract function render();
}
