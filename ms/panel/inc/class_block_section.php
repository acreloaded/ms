<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

class PageSectionFoot extends PageBlock{
	public $children = array(), $text;

	function __construct( $t ) {
		$this->text = $t;
	}

	function render() {
		$out = '<p class="post-footer">';
		foreach ( $this->children as $child )
			$out .= $child->render();
		return "$out<span class=\"date\">{$this->text}</span></p>";
	}
}

class PageSubSection extends PageBlock{
	public $children = array(), $text;

	function add1( PageBlock $i ) {
		$this->children[] = $i;
	}

	function add( array $a ) {
		foreach ( $a as $i ) $this->add1( $i );
	}

	function __construct( $t ) {
		$this->text = $t;
	}

	function render() {
		$out = "<p><h3>{$this->text}</h3></p><p>";
		foreach ( $this->children as $child )
			$out .= $child->render();
		return "$out</p>";
	}
}

final class PageSection extends PageBlock{
	private $title, $children, $foot = null;

	function add1( PageBlock $i ) {
		$this->children[] = $i;
	}

	function add( array $a ) {
		foreach ( $a as $i ) $this->add1( $i );
	}

	function bar( $title ) {
		$this->foot = new PageSectionFoot( $title );
	}

	function note( PageBlock $block ) {
		if ( $this->foot ) $this->foot->children[] = $block;
	}

	function notes( array $a ) {
		foreach ( $a as $i ) $this->note( $i );
	}

	static function needsCast( $i ) {
		return $i !== (array) $i;
	}

	function __construct( $title, $children = array() ) {
		$this->title = $title;
		$this->children = self::needsCast( $children ) ? array( $children ) : $children;
	}

	function render() {
		$out = "<h1>{$this->title}</h1>";
		foreach ( $this->children as $child )
			$out .= $child->render();
		if ( $this->foot )
			$out .= $this->foot->render();
		return "$out";
	}
}
