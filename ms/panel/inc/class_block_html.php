<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// dependencies
require_once 'class_block.php';

class HTMLBlock extends PageBlock{
	protected $content;

	function __construct( $h ) {
		$this->content = $h;
	}

	function render() {
		return (string)$this->content;
	}
}
