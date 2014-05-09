<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// dependencies
require_once 'class_block_html.php';

class Blockquote extends HTMLBlock{
	function render() {
		return "<blockquote><p>$this->content</p></blockquote>";
	}
}
