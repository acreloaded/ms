<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// dependencies
require_once 'class_pagepart.php';
require_once 'class_blocks.php';

class PageBody extends PagePart{
	public $front = '', $back = '';
	private $sections = array();

	function add_section( PageSection $b ) {
		$this->sections[] = $b;
	}

	function output( array $opts ) {
		$out = $this->front;
		foreach ( $this->sections as $section ) $out .= $section->render();
		return $out.$this->back;
	}
}
