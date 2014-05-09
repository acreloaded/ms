<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// dependencies
require_once 'class_block_html.php';

class Paragraph extends HTMLBlock{
	function render() {
		return "<p>$this->content</p>";
	}
}
