<?php
/*
Plugin Name: Ozh' Admin Drop Down Menu
Plugin URI: http://planetozh.com/blog/my-projects/wordpress-admin-menu-drop-down-css/
Description: Replaces admin menus with a CSS dropdown menu bar. Saves lots of clicks and page loads! <strong>For WordPress 2.5+</strong>
Version: 2.2.1
Author: Ozh
Author URI: http://planetOzh.com/
*/

/* Release History :
 * 1.0:       Initial release
 * 1.1:       Tiger Admin compatibility !
 * 1.2:       Multiple Page Plugin (ex: Akismet) compatibility and minor CSS improvements
 * 1.3:       Fix for plugins with subfolders on Windows WP installs
 * 1.3.1:     Minor CSS tweaks
 * 2.0:       Complete rewrite for WordPress 2.5
 * 2.0.1:     Fixed: bug with uploader
 * 2.0.2:     Improved: compatibility with admin custom CSS (some colors are now dynamically picked)
              Fixed: bug with submenu under plugin toplevel menus
              Fixed: WP's internal behavior or rewriting the "Manage" link according to the current "Write" page and vice-versa (makes sense?:)
			  Added: Option to display original submenu, per popular demand
 * 2.0.3:     Fixed: CSS bug with uploader, again. Grrrr.
 * 2.1:		  Added: WordPress Mu compatibility \o/
              Fixed: CSS issues with IE7, thanks Stuart
			  Added: Ability to dynamically resize menu on two lines when too many entries.
			  Added: Option to set max number of submenu entries before switching to horizontal display
 * 2.2:		  Fixed: Compatibilty with WP 2.6 (thanks to Matt Robenolt from ydekproductions.com for saving me some time:)
			  Added: Option page
			  Improved: compatibility with handheld devices
			  Improved: File structure for minimal memory footprint
 * 2.2.1:     Improved: some CSS tweaks (thanks to Dan Rubin)
			  Improved: the comment bubble now points to moderation
			  Improved: compatibility with Fluency (and even fixing stuff on the Fluency side)
 */


/***** Hook things in when visiting an admin page. When viewing a blog page, nothing even loads in memory. ****/

if (is_admin()) {
	global $wp_ozh_adminmenu;
	require_once(dirname(__FILE__).'/inc/core.php');
	add_action('init', create_function('', 'wp_enqueue_script("jquery");')); // Make sure jQuery is always loaded
	add_action('plugins_loaded', 'wp_ozh_adminmenu_init');	// Init plugin defaults or read options
	add_action('admin_menu', 'wp_ozh_adminmenu_add_page', -999); // Add option page
	add_action('dashmenu', 'wp_ozh_adminmenu'); // Replace the menus with our stuff
	add_action('admin_head', 'wp_ozh_adminmenu_head', 999); // Insert CSS & JS in <head>
	add_action('in_admin_footer', 'wp_ozh_adminmenu_footer'); // Add unobstrusive credits in footer
	
	global $wpmu_version;
	if ($wpmu_version) {
		require_once(dirname(__FILE__).'/inc/mu.php');
		add_action( '_admin_menu', 'wp_ozh_adminmenu_remove_blogswitch_init', -100 ); // MU specific menu takeover
	}
}

?>