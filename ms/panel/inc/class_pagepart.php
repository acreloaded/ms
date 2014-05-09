<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

abstract class PagePart {
	abstract function output( array $opts );
	/*{
		return __CLASS__;
		//return get_class($this);
	}*/
}
