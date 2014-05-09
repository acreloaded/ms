<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// dependencies
require_once 'class_pagepart.php';

class Menu extends PagePart{
	public $blocks = array();

	function add1( $area, $text ) {
		$this->blocks[$area] = $text;
	}

	function add( array $array ) {
		foreach ( $array as $a => $t ) $this->add1( $a, $t );
	}

	function output( array $opts ) {
		$out = '<ul>';
		foreach ( $this->blocks as $area => $text )
			$out .= '<li'.( $area == $opts['area'] ? ' id="current"' : '' )."><a href=\"index.php?area=$area\">$text</a></li>";
		return $out.'</ul>';
	}
}
