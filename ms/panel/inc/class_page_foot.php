<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// dependencies
require_once 'class_pagepart.php';

class PageFoot extends PagePart{
	function output( array $opts ) {
		return <<<FOOT
<!-- footer starts here -->
<div id="footer">
	<p>
		&copy; 2010-2011 Victor Zheng
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="index.html">Home</a> |
		<a href="index.html">Logout</a> |
		<a href="http://validator.w3.org/check?uri=referer">XHTML</a> |
		<a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a>
	</p>
</div>
<!-- footer ends here -->
</body>
</html>
FOOT;
	}
}
