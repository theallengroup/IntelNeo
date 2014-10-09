/* 
 * vim: syn=css 
 * */
.std_num {
	width:100%;
	text-align:right;
}

@media print {
	.system_menu {
		display:none;
	}
	.noprint {
		display:none;
	}
}
@media screen {
	.system_menu {
		display:block;
	}
	.noprint {
		display:block;
	}
}

body 
{
	padding:0px;
	margin:0px;
	background-repeat:repeat-x;
	/*background-image:url("fondos/corporate.jpg");*/
	background-color:<?=$body_background_color?>;
	/*background-attachment:fixed;*/
	
}
.hst{
}
a
{
	outline: none;
	font-family:verdana;
	font-size:15;
	color:<?=$link_color?>;
	text-decoration:none;
}
a:visited {
	color:<?=$link_color?>;
}
a:focus {
	color:<?=$link_color?>;

}
.label
{
	font-family:tahoma;
	font-size:13;
}
.standard_title
{
	color:<?=$label_text_color?>
}

.standard_text
{
	color:<?=$label_text_color?>
}
.std_title_info
{
	font-family:tahoma;
	font-size:15;
}
.std_info
{
}
.make_link
{
}
.standard_link
{
	color:<?=$link_color?>;
	cursor:pointer;
}
.standard_link:visited
{
	color:<?=$link_color?>;
	cursor:pointer;
}
.standard_link:hover
{
}

.kid_add_link {
	padding:2px;
	display:block;
	text-align:right;
}

/* Menu inicio*/

.default_menu_title
{
	line-height: 2.5;
}
.menu_link
{
	font-weight:bold;
}
.menu_link:visited {
	
}
.default_menu_ul a.menu_link:hover 
{
}
/*TODO.default_menu_li
{
	border-bottom:1px solid gray;
}*/
.form_flabel/*no funciona*/
{
	font-weight:bold;
	color:<?=$label_text_color?>;
}
.form_finput
{
	border:1px solid <?=$border_color?>;
	font-weight:bold;
}
.form_finput:focus
{
	background-color:<?=$focus_color?>;
	border:1px solid <?=$border_color?>;
	font-weight:bold;
}
.standard_text .form_flabel
{
}
/* Menu fin */

/* Inicio encabezado de la lista*/

.list_head
{
	background-color:<?=$list_head?>;
	text-decoration:none;
	border-color: <?=$list_head_border?>;
	border-width: 1px;
}
.list_link
{
}
.list_link:visited
{
	/*text-decoration:underline;*/
}
.list_title
{
	text-decoration:none;
}
.page_container 
{
	color:<?=$label_text_color?>
}
.list_footer
{
	color:<?=$label_text_color?>
}
/* Fin encabezado de la lista*/

/* forms inicio*/

.form_ok {
	background-color:<?=$button_background_color?> ;
	color:<?=$button_text_color?> ;
	border:1px solid;
}
.form_ok:focus {
	background-color:<?=$focus_color?> ;
}
.form_title
{
	text-decoration:none;
}

/* forms fin*/
.warning_container{
	border: 1px solid <?=$border_color?>;
	background-color:<?=$highlight_color?>;
} 
.warning_text{
	color:<?=$label_text_color?>;
}

/* inicio listado*/
.list_highlight {
	background-color:<?=$highlight_color?> ;
}
.list_cell
{
	color:<?=$label_text_color?>
}
.list_table
{
}
.name_column_link
{
}
.name_column_link:visited
{
}
.name_column_link:hover
{
}
.foreign_column_link
{
}
.foreign_column_link:visited
{
}
.foreign_column_link:hover
{
}
.action_link
{
}



/* fin listado*/


.tab_link {
	color:<?=$title_color ?>
}
.tab_link:active {
	color:<?=$title_color ?>;
}
.tab_link:visited {
	color:<?=$title_color ?>
}
.tab_link_span {
}

.tab_link_on {
	-moz-border-radius-topright:4px;
	-moz-border-radius-topleft:4px;
	position:relative;
	left:<?= -$border_width-1 ?>px;
	top:<?=2*$border_width+1?>px;
	margin:2px;
	padding:5px;
	border: <?=$border_width?>px solid <?=$border_color?>;
	border-bottom:<?=$border_width?>px solid <?=$background_color?>;
	background-color:<?=$background_color?>;
}
.tab_link_off{
	-moz-border-radius-topright:4px;
	-moz-border-radius-topleft:4px;
	position:relative;
	left:<?= -$border_width-1 ?>px;
	top:<?=2*$border_width?>px;
	margin:1px;
	padding:5px;
	border: <?=$border_width?>px solid <?=$border_alt_color?>;
	background-color:<?=$background_alt_color?>;
	border-bottom:<?=$border_width?>px solid <?=$background_alt_color?>;
}
.tab_on
{
border: <?=$border_width?>px solid <?=$border_color?>;
}
.tab_off
{
border: <?=$border_width?>px solid <?=$border_color?>;
	/* invisible */
}
.cool_container {
	border: <?=$border_width?>px solid <?=$border_color?>;
}
.tab_content
{
	color:<?=$title_color ?>
	border: <?=$border_width?>px solid <?=$border_color?>;
}

.pietabla
{
}

.shadow {
	margin:2px;
	background-color:<?=$background_color?>;
}
.menu{
	margin:2px;
	background-color:<?=$menu_background_color?>;
}

.round {
	margin:2px;
	background-color:<?=$round_background_color?>;
}

.iframe {
	margin:2px;
	background-color:<?=$iframe_background_color?>;
}

.banded {
	margin:2px;
	background-color:<?=$banded_background_color?>;
}


