<?php
class feed {
	function feed($me,$view){
		
	}
	function get_feed(){
		header('Content-Type: application/rss+xml');
echo('<?xml version="1.0"?>
<rss version="0.91">
  <channel>
    <title>scottandrew.com JavaScript and DHTML Channel</title>
    <link>http://www.scottandrew.com</link>
    <description>DHTML, DOM and JavaScript snippets from scottandrew.com</description>
    <language>en-us</language>
    <item>
      <title>DHTML Animation Array Generator</title>
      <description>Robert points us to the first third-party tool for the DomAPI: The Animation Array Generator, a visual tool for creating...</description>
      <link>http://www.scottandrew.com/weblog/2002_06#a000395</link>
    </item>
    <item>
      <title>DOM and Extended Entries</title>
      <description>Aarondot: A Better Way To Display Extended Entries. Very cool, and uses the DOM and JavaScript to reveal the extended...</description>
      <link>http://www.scottandrew.com/weblog/2002_06#a000373</link>
    </item>
    <item>
      <title>cellspacing and the DOM</title>
      <description>By the way, if you\'re using the DOM to generate TABLE elements, you have to use setAttribute() to set the...</description>
      <link>http://www.scottandrew.com/weblog/2002_05#a000365</link>
    </item>
    <item>
      <title>contenteditable for Mozilla</title>
      <description>The folks art Q42, creator of Quek (cute little avatar/chat) and Xopus (browser-based WYSIWYG XML-editor) have released code that simulates...</description>
      <link>http://www.scottandrew.com/weblog/2002_05#a000361</link>
    </item>
  </channel>
</rss>');
		
	}
}
?>
