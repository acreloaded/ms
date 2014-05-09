<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// dependencies
require_once "class_block.php";

class ListBlock extends PageBlock{
	protected $order, $items;

	function __construct( $order = false, $items = array() ) {
		$this->order = $order;
		$this->items = $items;
	}

	function add1( PageBlock $i ) {
		$this->items[] = $i;
	}

	function add( array $a ) {
		foreach ( $a as $i ) $this->add1( $i );
	}

	function render() {
		$tag = $this->order ? 'ol' : 'ul';
		$out = '';
		foreach ( $this->items as $item )
			$out .= '<li><span>'.$item->render().'</span></li>';
		return "<$tag>$out</$tag>";
	}
}

class OrderedList extends ListBlock{
	function __construct( $items = array() ) {
		parent::__construct( true, $items );
	}
}

class UnorderedList extends ListBlock{
	function __construct( $items = array() ) {
		parent::__construct( false, $items );
	}
}
