<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// dependencies
require_once 'class_block_html.php';

class CodeBlock extends HTMLBlock{
	function render() {
		return "<code>$this->content</code>";
	}
}
