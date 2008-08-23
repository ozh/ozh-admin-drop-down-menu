<?php
/*
Part of Plugin: Ozh' Admin Drop Down Menu
http://planetozh.com/blog/my-projects/wordpress-admin-menu-drop-down-css/
*/

function make_link_relative( $link ) {
	return preg_replace('|https?://[^/]+(/.*)|i', '$1', $link );
}

// Get vars & needed links, make them relative to be sure no one will be leeching icons or anything from someone else
$admin   = make_link_relative( $_GET['admin'] );
$plugin  = make_link_relative( $_GET['plugin'] );
$icons   = ($_GET['icons'] == 1) ? true : false ;
$fluency = ($_GET['fluency'] == 1) ? true : false;
$submenu = ($_GET['submenu'] == 1) ? true : false;
$mu      = ($_GET['mu'] == 1) ? true : false;

header('Content-type:text/css');
?>

/* Restyle or hide original items */
#sidemenu, #adminmenu, #dashmenu {
	display:none;
}
#media-upload-header #sidemenu li {
	display:auto;
}
#wphead h1 {
	font-size:25px;
}
#wphead #viewsite {
	margin-top: 6px;
}
#wphead #viewsite a {
	font-size:10px;
}
/* Styles for our new menu */
#ozhmenu { /* our new ul */
	font-size:12px;
	left:0px;
	list-style-image:none;
	list-style-position:outside;
	list-style-type:none;
	margin:0pt;
	padding-left:8px;
	position:absolute;
	top:4px;
	width:95%; /* width required for -wtf?- dropping li elements to be 100% wide in their containing ul */
	overflow:show;
	z-index:80;
}
#ozhmenu li { /* all list items */
	display:inline;
	line-height:200%;
	list-style-image:none;
	list-style-position:outside;
	list-style-type:none;
	margin:0 3px;
	padding:0;
	white-space:nowrap;
	float: left;
	width: 1*; /* maybe needed for some Opera ? */
}
#ozhmenu a { /* all links */
	text-decoration:none;
	color:#bbb;
	line-height:220%;
	padding:0px 10px;
	display:block;
	width:1*;  /* maybe needed for some Opera ? */
}
#ozhmenu li:hover,
#ozhmenu li.ozhmenu_over,
#ozhmenu li .current {
	background-color: #14568A;
	-moz-border-radius-topleft: 3px;
	-moz-border-radius-topright: 3px;
	-webkit-border-top-left-radius:3px;
	-webkit-border-top-right-radius:3px;
	border-top-left-radius:3px;
	border-top-right-radius:3px;
	color: #ddd;
}
#ozhmenu .ozhmenu_sublevel a:hover,
#ozhmenu .ozhmenu_sublevel a.current,
#ozhmenu .ozhmenu_sublevel a.current:hover {
	background-color: #e4f2fd;
	-moz-border-radius-topleft: 0px;
	-moz-border-radius-topright: 0px;
	-webkit-border-top-left-radius:0;
	-webkit-border-top-right-radius:0;
	border-top-left-radius:0;
	border-top-right-radius:0;
	color: #555;
}
#ozhmenu li ul { /* drop down lists */
	padding: 0;
	margin: 0;
	padding-bottom:5px;
	list-style: none;
	position: absolute;
	background: white;
	opacity:0.95;
	filter:alpha(opacity=95);
	border-left:1px solid #c6d9e9 ;
	border-right:1px solid #c6d9e9 ;
	border-bottom:1px solid #c6d9e9 ;
	-moz-border-radius-bottomleft:5px;
	-moz-border-radius-bottomright:5px;
	-webkit-border-bottom-left-radius:5px;
	-webkit-border-bottom-right-radius:5px;
	border-bottom-left-radius:5px;
	border-bottom-right-radius:5px;
	width: 1*;  /* maybe needed for some Opera ? */
	min-width:6em;
	left: -999em; /* using left instead of display to hide menus because display: none isn't read by screen readers */
	list-style-position:auto;
	list-style-type:auto;
}
#ozhmenu li ul li { /* dropped down lists item */
	background:transparent !important;
	float:none;
	text-align:left;
}
#ozhmenu li ul li a { /* links in dropped down list items*/
	margin:0px;
	color:#666;
}
#ozhmenu li:hover ul, #ozhmenu li.ozhmenu_over ul { /* lists dropped down under hovered list items */
	left: auto;
	z-index:999999;
}
#ozhmenu li a #awaiting-mod, #ozhmenu li a #update-plugins {
	position: absolute;
	margin-left: 0.1em;
	font-size: 0.8em;
	background-image: url(<?php echo $admin; ?>/images/comment-stalk-fresh.gif);
	background-repeat: no-repeat;
	background-position: -160px bottom;
	height: 1.7em;
	width: 1em;
}
#ozhmenu li.ozhmenu_over a #awaiting-mod, #ozhmenu li a:hover #awaiting-mod, #ozhmenu li.ozhmenu_over a #update-plugins, #ozhmenu li a:hover #update-plugins {
	background-position: -2px bottom;
}
#ozhmenu li a #awaiting-mod span, #ozhmenu li a #update-plugins span {
	color: #fff;
	top: -0.3em;
	right: -0.5em;
	position: absolute;
	display: block;
	height: 1.3em;
	line-height: 1.3em;
	padding: 0 0.8em;
	background-color: #2583AD;
	-moz-border-radius: 4px;
	-khtml-border-radius: 4px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
}
#ozhmenu li.ozhmenu_over a #awaiting-mod span, #ozhmenu li a:hover #awaiting-mod span, #ozhmenu li.ozhmenu_over a #update-plugins span, #ozhmenu li a:hover #update-plugins span {
	background-color:#D54E21;
}
#ozhmenu .current {
	border:0px; /* MSIE insists on having this */
}
#ozhmenu li ul li a.current:before {
	content: "\00BB \0020";
	color:#d54e21;
}
/* Mu Specific */
#ozhmumenu_head {
	color:#bbb;
	font-weight:bolder;
}
#ozhmumenu_head #all-my-blogs {
	position:relative;
	top:0px;
	background:#ffa;
	color:#000;
}
/* Just for IE7 */
#wphead {
	#border-top-width: 31px;
}
#media-upload-header #sidemenu { display: block; }

<?php if (!$submenu) { ?>
/* Hide vanilla submenu */
#wpwrap #submenu li {display:none;}
<?php } ?>

<?php if ($fluency) { ?>
/* Fluency compat + fixes */
#TB_overlay {z-index:99001;}
#TB_window {z-index:99002;}
<?php } ?>

<?php if ($fluency && $icons) { ?>
#ozhmenu li.ozhmenu_toplevel ul li.ozhmenu_sublevel a {padding-left:22px;}
<?php } ?>

<?php if ($icons) {
	require(dirname(__FILE__).'/icons.php');
?>
/* Icons */
#ozhmenu .ozhmenu_sublevel a {
	padding-left:22px;
	background-repeat:no-repeat;
	background-position:3px center;
}
.oam_plugin a {
	background-image:url(<?php echo $plugin; ?>/images/plugin.png);
}
<?php
	foreach($wp_ozh_adminmenu['icon_names'] as $link=>$icon) {
		$link = str_replace(array('.php','.','/'),array('','_','_'),$link);
		echo "#oamsub_$link a {background-image:url($plugin/images/$icon.png);}\n";
	}

} ?>

<?php if ($mu && $icons) { ?>
#ozhmumenu .ozhmenu_sublevel a {background-image:url(<?php echo $plugin; ?>/images/world_link.png);}
<?php
	foreach($wp_ozh_adminmenu['icon_names_mu'] as $link=>$icon) {
		$link = str_replace(array('.php','.','/'),array('','_','_'),$link);
		echo "#oamsub_$link a {background-image:url($plugin/images/$icon.png);}\n";
	}

} ?>

/**/