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
	// echo "<pre>";echo htmlentities(print_r($_POST,true));echo "</pre>";	
	
	switch ($_POST['action']) {
	case 'update_options':
	
		$options['display_submenu'] = ($_POST['oam_displaysub']) ? '1' : '0';
		$options['toplinks'] = ($_POST['oam_toplinks'])? '1' : '0';
		$options['icons'] = ($_POST['oam_icons'])? '1' : '0';
		$options['too_many_plugins'] = intval($_POST['oam_too_many_plugins']);
		
		if (!update_option('ozh_adminmenu', $options))
			add_option('ozh_adminmenu', $options);
			
		$wp_ozh_adminmenu = array_merge( (array)$wp_ozh_adminmenu, $options );
		
		$msg = wp_ozh_adminmenu__("updated");
		break;

	case 'reset_options':
		delete_option('ozh_adminmenu');
		$msg = wp_ozh_adminmenu__("deleted");
		break;
	}

	echo '<div id="message" class="updated fade">';
	echo '<p>'.sprintf(wp_ozh_adminmenu__('Admin Drop Down Menu settings <strong>%s</strong>'), $msg)."</p>\n";
	echo "</div>\n";
	wp_ozh_adminmenu_head(false);
}

function wp_ozh_adminmenu_options_page() {
	global $wp_ozh_adminmenu;
	
	if (isset($_POST['ozh_adminmenu']) && ($_POST['ozh_adminmenu'] == 1) )
		wp_ozh_adminmenu_processform();
	
	wp_ozh_adminmenu_init();
	
	// Load translation file if any
	$locale = get_locale();
	$mofile = WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/translations/adminmenu' . '-' . $locale . '.mo';
	load_textdomain('adminmenu', $mofile);	
	
	//echo "<pre>".wp_ozh_adminmenu_sanitize(print_r($wp_ozh_adminmenu,true))."</pre>";
	
	$checked_displaysub = ($wp_ozh_adminmenu['display_submenu'] == 1) ? 'checked="checked"' : '' ;
	$checked_icons = ($wp_ozh_adminmenu['icons'] == 1) ? 'checked="checked"' : '' ;
	$checked_toplinks = ($wp_ozh_adminmenu['toplinks'] == 1) ? 'checked="checked"' : '' ;
	$too_many_plugins = intval($wp_ozh_adminmenu['too_many_plugins']);
	
	echo '
	<style type="text/css">
	.wrap {margin-bottom:2em}
	</style>
    <div class="wrap">
    <h2>Admin Drop Down Menu</h2>
    <form method="post" action="">
	';
	wp_nonce_field('ozh-adminmenu');
?>
	<table class="form-table"><tbody>
	<input type="hidden" name="ozh_adminmenu" value="1"/>
    <input type="hidden" name="action" value="update_options">
	
    <tr><th scope="row"><?php echo wp_ozh_adminmenu__('Icons'); ?></th>
	<td><label><input type="checkbox" <?php echo $checked_icons; ?> name="oam_icons"> <?php echo wp_ozh_adminmenu__('Display menu icons');?></label><br/>
	<?php printf(wp_ozh_adminmenu__("They're so cute (and they're from %s)"),'<a href="http://www.famfamfam.com/">famfamfam</a>'); ?>
	</td></tr>

    <tr><th scope="row"><?php echo wp_ozh_adminmenu__('Submenus'); ?></th>
	<td><label><input type="checkbox" <?php echo $checked_displaysub; ?> name="oam_displaysub"> <?php echo wp_ozh_adminmenu__('Display sub menus the regular way'); ?></label><br/>
	<?php echo wp_ozh_adminmenu__("Some like it better when sub menus don't even need you to hover the top menu link"); ?>
	</td></tr>
	
    <tr><th scope="row"><?php echo wp_ozh_adminmenu__('Break Long Lists'); ?></th>
	<td><label><?php printf(wp_ozh_adminmenu__('Break if more than %s menu entries'), "<input type=\"text\" value=\"$too_many_plugins\" size=\"2\" name=\"oam_too_many_plugins\">"); ?></label><br/>
	<?php echo wp_ozh_adminmenu__('If a dropdown gets longer than this value, it will switch to horizontal mode so that it will hopefully fit in your screen (requires javascript)'); ?>
	</td></tr>

    <tr><th scope="row"><?php echo wp_ozh_adminmenu__('Top Links'); ?></th>
	<td><label><input type="checkbox" <?php echo $checked_toplinks; ?> name="oam_toplinks"> <?php echo wp_ozh_adminmenu__('Make top links clickable'); ?></label><br/>
	<?php echo wp_ozh_adminmenu__('Uncheck this option to improve compatibility with browsers that cannot handle the "hover" event (<em>ie</em> most handheld devices)'); ?>
	</td></tr>
	
    <tr><th scope="row"><?php echo wp_ozh_adminmenu__('Give Some &hearts;'); ?></th>
	<td><?php printf(wp_ozh_adminmenu__('Do you like this plugin? Then <a href="%s">rate it 5 Stars</a> on the official Plugin Directory!'),'http://wordpress.org/extend/plugins/ozh-admin-drop-down-menu/'); ?><br/>
	<?php echo printf(wp_ozh_adminmenu__('Do you <em>love</em> this plugin? Please <a href="%s">blog about it</a>! Tell your readers you like it so they will discover, try and hopefully like it too&nbsp;:)'),'post-new.php'); ?><br/>
	<?php echo printf(wp_ozh_adminmenu__('Are you <span id="totallycrazy">crazy</span> about this plugin? <a href="%s">Paypal me a beer</a>! Every donation warms my heart and motivates me to release free stuff!'),'http://planetozh.com/exit/donate'); ?>
	</td></tr>
	
	<script type="text/javascript">
	function oam_dance() {
		var fontstyle, delay;
		if (jQuery('#totallycrazy').css('font-style') == 'italic') {
			fontstyle = 'normal';
			delay = 1200;		
		} else {
			fontstyle = 'italic';
			delay = 200;		
		}
		jQuery('#totallycrazy').css('font-style',fontstyle);
		oam_danceagain(delay);
	}
	function oam_danceagain(delay) {setTimeout(function(){oam_dance();}, delay);}
	oam_danceagain(100);	
	</script>

	</tbody></table>
	
	<p class="submit">
	<input name="submit" value="<?php echo wp_ozh_adminmenu__('Save Changes');?>" type="submit" /> <?php echo wp_ozh_adminmenu__('(might need a page refresh here)');?>
	</p>

	</form>
	</div>
	
	<div class="wrap"><h2><?php echo wp_ozh_adminmenu__('Reset Settings');?></h2>
	<form method="post" action="">

<?php
	wp_nonce_field('ozh-adminmenu');
?>
	<input type="hidden" name="ozh_adminmenu" value="1"/>
    <input type="hidden" name="action" value="reset_options">

	<p><?php echo wp_ozh_adminmenu__('Clicking the following button will remove all the settings for this plugin from your database. You might want to do so in the following cases:');?></p>
	<ul>
	<li><?php echo wp_ozh_adminmenu__('you want to uninstall the plugin and leave no unnecessary entries in your database.');?></li>
	<li><?php echo wp_ozh_adminmenu__('you want all settings to be reverted to their default values');?></li>
	</ul>
	<p class="submit" style="border-top:0px;padding:0;"><input style="color:red" name="submit" value="<?php echo wp_ozh_adminmenu__('Reset Settings');?>" onclick="return(confirm('<?php echo js_escape(wp_ozh_adminmenu__('Really do?'));?>'))" type="submit" /></p>
	<p><?php echo wp_ozh_adminmenu__('There is no undo, so be very sure you want to click the button!');?></p>
	
	</form>
	</div>
<?php

}

// Translation wrapper
function wp_ozh_adminmenu__($string) {
	return __($string, 'adminmenu');
}


// Sanitize string for display: escape HTML but preserve UTF8 (or whatever)
function wp_ozh_adminmenu_sanitize($string) {
	return stripslashes(attribute_escape($string));
	//return stripslashes(htmlentities($string, ENT_COMPAT, get_bloginfo('charset')));
}

?>