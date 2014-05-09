<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// dependencies
require_once 'class_pagepart.php';

class Sidebar extends PagePart{
	public $titles = array(), $items = array();
	protected $index = -1;

	function add( $title ) {
		$this->titles[++$this->index] = $title;
		$this->items[$this->index] = array();
	}

	function append( $module, $item ) {
		$this->items[$this->index][$module] = $item;
	}

	function set_module( &$dst, $src, $default = null ) {
		$dst = $src;
		$found = false;
		foreach ( $this->items as $item )
			if ( isset( $item[$dst] ) ) {
				$found = true;
				break;
			}
		if ( !$found )
			if ( $default !== null ) $dst = $default;
			else {
				reset( $this->items[0] );
				$dst = key( $this->items[0] );
			}
	}

	function output( array $opts ) {
		$out = '';
		foreach ( $this->titles as $index => $title ) {
			$out .= "<h1>$title</h1><ul class=\"sidemenu\">";
			foreach ( $this->items[$index] as $module => $item ) {
				$a = $opts['module'] == $module ? '<strong><span>' : "<a href=\"index.php?area={$opts['area']}&module=$module\">";
				$b = $opts['module'] == $module ? '</span></strong>' : '</a>';
				$out .= "<li>$a$item$b</li>";
			}
			$out .= '</ul>';
		}
		return $out;
	}
}
