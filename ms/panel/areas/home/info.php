<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// DEBUG

$section = new PageSection( 'Template Info' );
$sections = array();
$sections[] = new Paragraph(
	<<<LOL1
<strong>This</strong> is a free, W3C-compliant, CSS-based website template
by <a href="http://styleshout.com">styleshout</a>.
LOL1
);
$sections[] = new Paragraph(
	<<<LOL2
You can find more of my free template designs at <a href="http://styleshout.com/">his website</a>.
For premium commercial designs, you can check out <a href="http://dreamtemplate.com">DreamTemplate</a>.
LOL2
);

$section->add( $sections );

$section->bar( 'Sep 15, 2006' );
$section->note( new HTMLBlock( '<a href='' class="readmore">Read more</a>' ) );
$section->note( new HTMLBlock( '<a href='' class="comments">Comments (7)</a>' ) );

$page->add( $section );

// DEBUG 2

$section = new PageSection( 'Sample Tags' );

$section->add1( $section2 = new PageSubSection( 'Code' ) );
$section2->add1( new CodeBlock(
		'code-sample { <br />
font-weight: bold;<br />
font-style: italic;<br />
}' ) );

$section->add1( $section2 = new PageSubSection( 'Example Lists' ) );
$section2->add1( $section3 = new OrderedList );
$section3->add( array(
		new HTMLBlock( 'example of ordered list' ),
		new HTMLBlock( 'uses span to color the numbers' ),
	) );
$section2->add1( $section3 = new UnorderedList );
$section3->add( array(
		new HTMLBlock( 'example of unordered list' ),
		new HTMLBlock( 'uses span to color the numbers' ),
	) );

$section->add1( $section2 = new PageSubSection( 'Blockquote' ) );
$section2->add1( new Blockquote(
		'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy
nibh euismod tincidunt ut laoreet dolore magna aliquam erat....'
	) );

$section->add1( $section2 = new PageSubSection( 'Images and Text' ) );
$section2->add1( new HTMLBlock(
		'<a href="http://getfirefox.com/"><img src="'.$config['acdn'].'/firefox-gray.jpg" width="100" height="120" alt="firefox" class="float-left" /></a>
Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec libero. Suspendisse bibendum.
Cras id urna. Morbi tincidunt, orci ac convallis aliquam, lectus turpis varius lorem, eu
posuere nunc justo tempus leo. Donec mattis, purus nec placerat bibendum, dui pede condimentum
odio, ac blandit ante orci ut diam. Cras fringilla magna. Phasellus suscipit, leo a pharetra
condimentum, lorem tellus eleifend magna, eget fringilla velit magna id neque. Curabitur vel urna.
In tristique orci porttitor ipsum. Aliquam ornare diam iaculis nibh. Proin luctus, velit pulvinar
ullamcorper nonummy, mauris enim eleifend urna, congue egestas elit lectus eu est.'
	) );

$section->add1( $section2 = new PageSubSection( 'Example Form' ) );
/*
<form action="#">
	<p>
	<label>Name</label>
	<input name="dname" value="Your Name" type="text" size="30" />
	<label>Email</label>
	<input name="demail" value="Your Email" type="text" size="30" />
	<label>Your Comments</label>
	<textarea rows="5" cols="5"></textarea>
	<br />
	<input class="button" type="submit" />
	</p>
</form>
*/

$page->add( $section );
