<?php
/*
Part of Plugin: Ozh' Admin Drop Down Menu
http://planetozh.com/blog/my-projects/wordpress-admin-menu-drop-down-css/
*/

global $wp_ozh_adminmenu;


function wp_ozh_adminmenu_processform() {

	global $wp_ozh_adminmenu;
	
	check_admin_referer('ozh-adminmenu');
	
	// Debug:
	//echo "<pre>";echo htmlentities(print_r($_POST,true));echo "</pre>";	
	
	switch ($_POST['action']) {
	case 'update_options':
	
		$options['display_submenu'] = ($_POST['oam_displaysub']) ? '1' : '0';
		$options['toplinks'] = ($_POST['oam_toplinks'])? '1' : '0';
		$options['too_many_plugins'] = intval($_POST['oam_too_many_plugins']);
		
		if (!update_option('ozh_adminmenu', $options))
			add_option('ozh_adminmenu', $options);
			
		$wp_ozh_adminmenu = array_merge( (array)$wp_ozh_adminmenu, $options );
		
		$msg = "updated";
		break;

	case 'reset_options':
		delete_option('ozh_adminmenu');
		$msg = "deleted";
		break;
	}

	echo '<div id="message" class="updated fade">';
	echo "<p>Admin Drop Down Menu settings <strong>$msg</strong></p>\n";
	echo "</div>\n";
	wp_ozh_adminmenu_head(false);
}

function wp_ozh_adminmenu_options_page() {
	global $wp_ozh_adminmenu;
	
	if (isset($_POST['ozh_adminmenu']) && ($_POST['ozh_adminmenu'] == 1) )
		wp_ozh_adminmenu_processform();
	
	wp_ozh_adminmenu_init();
	
	// echo "<pre>".wp_ozh_adminmenu_sanitize(print_r($wp_ozh_adminmenu,true))."</pre>";
	
	$checked_displaysub = ($wp_ozh_adminmenu['display_submenu'] == 1) ? 'checked="checked"' : '' ;
	$checked_toplinks = ($wp_ozh_adminmenu['toplinks'] == 1) ? 'checked="checked"' : '' ;
	$too_many_plugins = intval($wp_ozh_adminmenu['too_many_plugins']);
	
	echo <<<HTML
	<style type="text/css">
	.wrap {margin-bottom:2em}
	</style>
    <div class="wrap">
    <h2>Admin Drop Down Menu</h2>
    <form method="post" action="">
HTML;
	wp_nonce_field('ozh-adminmenu');
	echo <<<HTML
	<table class="form-table"><tbody>
	<input type="hidden" name="ozh_adminmenu" value="1"/>
    <input type="hidden" name="action" value="update_options">
	
    <tr><th scope="row">Submenus</th>
	<td><label><input type="checkbox" $checked_displaysub name="oam_displaysub"> Display sub menus the regular way</label><br/>
	Some like it better when sub menus don't even need you to hover the top menu link (might need a <a href="options-general.php?page=ozh_admin_menu">page refresh</a> here)
	</td></tr>
	
    <tr><th scope="row">Break Long Lists</th>
	<td><label>Break if more than <input type="text" value="$too_many_plugins" size="2" name="oam_too_many_plugins"> menu entries</label><br/>
	If a dropdown gets longer than this value, it will switch to horizontal mode so that it will hopefully fit in your screen (requires javascript)
	</td></tr>

    <tr><th scope="row">Top Links</th>
	<td><label><input type="checkbox" $checked_toplinks name="oam_toplinks"> Make top links clickable</label><br/>
	Uncheck this option to improve compatibility with browsers that cannot handle the "hover" event (<em>ie</em> most handheld devices)
	</td></tr>
	
    <tr><th scope="row">Give Some &hearts;</th>
	<td>Do you like this plugin? Please <a href="post-new.php">blog about it</a>! Tell your readers you like it so they will discover, try and hopefully like it too&nbsp;:)<br/>(and if you definitely <em>love</em> this plugin, you could also consider <a href="http://planetozh.com/exit/donate">buying me a beer</a>!)
	</td></tr>

	</tbody></table>
	
	<p class="submit">
	<input name="submit" value="Save Changes" type="submit" />
	</p>

	</form>
	</div>
	
	<div class="wrap"><h2>Reset Settings</h2>
	<form method="post" action="">
HTML;
	wp_nonce_field('ozh-adminmenu');
	echo <<<HTML2
	<input type="hidden" name="ozh_adminmenu" value="1"/>
    <input type="hidden" name="action" value="reset_options">

	<p>Clicking the following button will remove all the settings for this plugin from your database. You might want to do so in the following cases:</p>
	<ul>
	<li>you want to uninstall the plugin and leave no unnecessary entries in your database.</li>
	<li>you want all settings to be reverted to their default values</li>
	</ul>
	<p class="submit" style="border-top:0px;padding:0;"><input style="color:red" name="submit" value="Reset Settings" onclick="return(confirm('Really do?'))" type="submit" /></p>
	<p>There is no undo, so be very sure you want to click the button!</p>
	
	</form>
	</div>
HTML2;

}

// Sanitize string for display: escape HTML but preserve UTF8 (or whatever)
function wp_ozh_adminmenu_sanitize($string) {
	return stripslashes(attribute_escape($string));
	//return stripslashes(htmlentities($string, ENT_COMPAT, get_bloginfo('charset')));
}

?>