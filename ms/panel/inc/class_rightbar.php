<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// dependencies
require_once 'class_pagepart.php';

class Rightbar extends PagePart{
	public $items = array();

	function add( $title, $text ) {
		$this->items[$title] = $text;
	}

	function add_quote( $title, $who, $words ) {
		$this->items[$title] = "&quot;$words&quot;</p><p class=\"align-right\">- $who";
	}

	function output( array $opts ) {
		$out = '';
		foreach ( $this->items as $title => $text ) {
			/*
			<h1>Wise Words</h1>
				<p>&quot;Keep away from people who try to belittle your ambitions. Small people
				always do that, but the really great make you feel that you, too, can
				become great.&quot;</p>
				<p class="align-right">- Mark Twain</p>
			*/
			$out .= "<h1>$title</h1><p>$text</p>";
		}
		return $out;
	}
}
