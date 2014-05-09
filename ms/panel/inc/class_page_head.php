<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// dependencies
require_once 'class_pagepart.php';

class PageHead extends PagePart{
	function output( array $opts ) {
		global $config;
		return <<<HEAD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>ACRMS Panel</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<meta name="author" content="Victor" />
<meta name="robots" content="noindex, nofollow, noarchive" />
<meta name="googlebot" content="noarchive" />
<link rel="stylesheet" type="text/css" media="screen" href="{$config['acdn']}/Orange.css" />
</head>
<body>
HEAD;
	}
}
