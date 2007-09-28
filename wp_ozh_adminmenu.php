<?php
/*
Plugin Name: Admin Drop Down Menu
Plugin URI: http://planetozh.com/blog/my-projects/wordpress-admin-menu-drop-down-css/
Description: Replaces admin menu and submenu with a 2 level horizontal CSS dropdown menu bar. Saves lots of clicks !
Version: 1.3
Author: Ozh
Author URI: http://planetOzh.com/
*/

/* Release History :
 * 1.0 : initial release
 * 1.1 : Tiger Admin compatibility !
 * 1.2 : Multiple Page Plugin (ex: Akismet) compatibility and minor CSS improvements
 * 1.3 : Fix for plugins with subfolders on Windows WP installs
 */

/* Main function : creates the new set of intricated <ul> and <li> */
function wp_ozh_adminmenu() {
	global $is_winIE;
	
	if (function_exists('wp_admin_tiger_css') and !$is_winIE) {
		$tiger = true;
	} else {
		$tiger = false;
	}

	$menu = wp_ozh_adminmenu_build ();
	
	$ozh_menu = '';
	$printsub = 1;
	
	foreach ($menu as $k=>$v) {
		$url 	= $v['url'];
		$name 	= $k;
		$anchor = $v['name'];
		$class	= $v['class'];

		if ($is_winIE)
			$ie_code = " onmouseover='this.className=\\\"msieFix\\\"' onmouseout='this.className=\\\"\\\"'";
		$ozh_menu .= '<li'.$ie_code."><a href='$url'$class>$anchor</a>";
		if (is_array($v['sub'])) {
			
			$ulclass='';
			if ($class) $ulclass = " class='ulcurrent'";
			$ozh_menu .= "<ul$ulclass>";

			foreach ($v['sub'] as $subk=>$subv) {
				$suburl = $subv['url'];
				$subanchor = $subv['name'];
				$subclass='';
				if (array_key_exists('class',$subv)) $subclass=$subv['class'];
				$ozh_menu .= "<li><a href='$suburl'$subclass>$subanchor</a></li>";
			}
			$ozh_menu .= "</ul>";
		} else {
			if (!$tiger) {
				$ozh_menu .= "<ul><li class='altmenu_empty' title='This menu has no sub menu'><small>&#8230;</small></li><li>&nbsp;</li><li>&nbsp;</li><li>&nbsp;</li></ul>";
				if ($class) $printsub = 0;
			}
		}
		$ozh_menu .="</li> ";
			
	}
	
	if (!$tiger) {
		wp_ozh_adminmenu_css($printsub);
	} else {
		wp_ozh_adminmenu_css_tiger($printsub);
	}
	
	wp_ozh_adminmenu_old_printjs($ozh_menu, $printsub, $tiger);
}

/* Core stuff : builds an array populated with all the infos needed for menu and submenu */
function wp_ozh_adminmenu_build () {
	global $menu, $submenu, $plugin_page, $pagenow;
	
	/* Most of the following garbage are bits from admin-header.php,
	 * modified to populate an array of all links to display in the menu
	 */

	$self = preg_replace('|^.*/wp-admin/|i', '', $_SERVER['PHP_SELF']);
	$self = preg_replace('|^.*/plugins/|i', '', $self);
	
	get_admin_page_parent();
	
	$altmenu = array();
	
	/* Step 1 : populate first level menu as per user rights */
	foreach ($menu as $item) {
		// 0 = name, 1 = capability, 2 = file
		if ( current_user_can($item[1]) ) {
			if ( file_exists(ABSPATH . "wp-content/plugins/{$item[2]}") )
				$altmenu[$item[2]]['url'] = get_settings('siteurl') . "/wp-admin/admin.php?page={$item[2]}";			
			else
				$altmenu[$item[2]]['url'] = get_settings('siteurl') . "/wp-admin/{$item[2]}";

			if (( strcmp($self, $item[2]) == 0 && empty($parent_file)) || ($parent_file && ($item[2] == $parent_file)))
			$altmenu[$item[2]]['class'] = " class='current'";
			
			$altmenu[$item[2]]['name'] = $item[0];

			/* Windows installs may have backslashes instead of slashes in some paths, fix this */
			$altmenu[$item[2]]['name'] = str_replace(chr(92),chr(92).chr(92),$altmenu[$item[2]]['name']);
		}
	}
	
	/* Step 2 : populate second level menu */
	foreach ($submenu as $k=>$v) {
		foreach ($v as $item) {
			if (array_key_exists($k,$altmenu) and current_user_can($item[1])) {
				
				// What's the link ?
				$menu_hook = get_plugin_page_hook($item[2], $k);
				if (file_exists(ABSPATH . "wp-content/plugins/{$item[2]}") || ! empty($menu_hook)) {
					if ( 'admin.php' == $pagenow )
						$link = get_settings('siteurl') . "/wp-admin/admin.php?page={$item[2]}";
					else
						$link = get_settings('siteurl') . "/wp-admin/{$k}?page={$item[2]}";
				} else {
					$link = get_settings('siteurl') . "/wp-admin/{$item[2]}";
				}
				/* Windows installs may have backslashes instead of slashes in some paths, fix this */
				$link = str_replace(chr(92),chr(92).chr(92),$link);
				
				$altmenu[$k]['sub'][$item[2]]['url'] = $link;
				
				// Is it current page ?
				$class = '';
				if ( (isset($plugin_page) && $plugin_page == $item[2] && $pagenow == $k) || (!isset($plugin_page) && $self == $item[2] ) ) $class=" class='current'";
				if ($class) {
					$altmenu[$k]['sub'][$item[2]]['class'] = $class;
					$altmenu[$k]['class'] = $class;
				}
				
				// What's its name again ?
				$altmenu[$k]['sub'][$item[2]]['name'] = $item[0];
			}
		}
	}
	
	/**
	// Uncomment to see how neat it is now !
	print "<pre>Our Oh-So-Beautiful-4-Levels-";
	print_r($altmenu);
	/**/

	return ($altmenu);
}

/* The javascript bits that replace the existing menu by our new one */
function wp_ozh_adminmenu_old_printjs ($admin = '', $sub = 1, $tiger=false) {
	print "<script>
	document.getElementById('adminmenu').innerHTML=\"$admin\";";
	if ($sub and !$tiger) print "document.getElementById('submenu').innerHTML=\"<li>&nbsp;</li>\"";
	print "</script>";
}

/* Print the CSS stuff. Modify with care if you want to customize colors ! */
function wp_ozh_adminmenu_css_tiger($sub = 1) {
	$id = '#adminmenu';
	
	if ($sub == 0) {
	$sub_opacity="$id li ul {
		opacity: 0.8;
		-moz-opacity: 0.8;
		filter: alpha(opacity=80);
	}
	";
	} else {$sub_opacity='';}
	
	print <<<CSS
	<style>
	#adminmenu li ul {
		position:absolute;
		left:-9000px;
	}
	#adminmenu li:hover {
		background:#DDD;
	}
	#adminmenu li:hover ul {
		left:140px;
		z-index:90;
		width:160px !important;
		padding:0;
		-moz-opacity:0.90;
	}
	#adminmenu li:hover ul li {
		position:relative;
		top:-2em;
		background: #555;
		padding:0;
		margin:0;
	}
	#adminmenu li:hover ul li a, #adminmenu li:hover ul li a.current {
		position:relative;
		top:-3px;
		left:-3px;
		width:170px;
		background:#DDD !important;
		color:#222;
	}
	#zadminmenu li:hover ul li a.current {
		background:none !important;
	}

	#adminmenu li:hover ul li a:hover {
		background:transparent url(../wp-content/plugins/wp-admin-tiger/wp-admin-tiger_files/ol_admin_images/bg_menu_on.jpg) !important;
	}
	$sub_opacity
	</style>

CSS;
}

function wp_ozh_adminmenu_css($sub = 1) {
	$id = '#adminmenu';
	
	if ($sub == 0) {
	$sub_opacity="$id li ul {
		opacity: 0.8;
		-moz-opacity: 0.8;
		filter: alpha(opacity=80);
	}
	";
	} else {$sub_opacity='';}
	
	print <<<CSS
	<style>
	#submenu {
		left:0px;
		margin:0;
		height:2em;
	}
	/* all lists */
	$id {
		height:2em;
	}
	$id li {
		padding:0 0.3em;
	}
	$id li ul li {
		background: #0d324f;
		border-bottom: none;
		line-height:160%;
	}
	$id li ul li a,$id li ul li a:link,$id li ul li a:visited {
		color: #9090EE;
		font-size: 12px;
		border-bottom: none;
	}
	$id li ul li a:hover {
		background: #ddeaf4;
		color: #393939;
	}
	$id,$id ul {
		padding:0;
	}
	/* Nested ULs */
	$id li ul {
		position:absolute;
		left:-900px;
	}
	/* All LIs */
	$id li {
		float:left;
		list-style-type:none;
	}
	$id li ul {
		background: #0d324f;
		padding: 3px 2em 0 2.9em;
	}
	$id li:hover ul,$id li.msieFix ul {
		left:0px;
		z-index:90;
		right:0px;
	}
	$id li .ulcurrent {
		left:0px;
		right:0px;
		z-index:89;
		width:auto;
	}
	.altmenu_empty {
		color:#6da6d1;
		height:1.5em;
	}
	$sub_opacity
	/* Stuff for MSIE */
	* html $id li .ulcurrent {width:200%;}
	* html $id li a {
		border-top:2px solid #6da6d1;
	}
	* html $id li ul li a {
		border:none;
	}
	* html $id li.msieFix ul, {
		margin:2em 0;
		width:300%;
		left:0;
	}
	* html $id li ul {
		margin:2em 0;
	}
	/**/
	</style>

CSS;
}


add_action('admin_footer', 'wp_ozh_adminmenu');

?>
