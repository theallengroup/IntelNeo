/*	sIFR 2.0.2
	Copyright 2004 - 2006 Mike Davidson, Shaun Inman, Tomas Jogin and Mark Wubben

	This software is licensed under the CC-GNU LGPL <http://creativecommons.org/licenses/LGPL/2.1/>
*/

// true unlocks .swf for usage on local networks (and testing locally)
// false locks .swf so it may only be served from a domain below

allowlocal = true;

// fill in whatever domains you want this to work on... must be exact matches... asterisk means all

allowedDomains = new Array("*","www.yourdomain.com","yourdomain.com");