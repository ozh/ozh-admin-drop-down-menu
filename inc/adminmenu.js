/*
Part of Plugin: Ozh' Admin Drop Down Menu
http://planetozh.com/blog/my-projects/wordpress-admin-menu-drop-down-css/
*/

// Resize menu to make sure it doesnt overlap with #user_info or blog title
function ozhmenu_resize() {
	// Reinit positions
	jQuery('#ozhmenu').css('width','');
	jQuery('#wphead').css('border-top-width', '30px');
	// Resize
	var ozh_w = parseInt(jQuery('#ozhmenu').css('width').replace(/px/,''));
	var info_w = parseInt(jQuery('#user_info').css('width').replace(/px/,'')) || 130; // the " or 130" part is for when width = 'auto' (on MSIE..) to get 130 instead of NaN
	jQuery('#ozhmenu').css('width', (ozh_w - info_w - 1)+'px' );
	var ozh_h = parseInt(jQuery('#ozhmenu').css('height').replace(/px/,''));
	// Compare positions of first & last top level lis
	var num_li=jQuery('#ozhmenu li.ozhmenu_toplevel').length;
	var first_li = jQuery('#ozhmenu li.ozhmenu_toplevel').eq(0).offset();
	var last_li = jQuery('#ozhmenu li.ozhmenu_toplevel').eq(num_li-1).offset(); // Dunno why, but jQuery('#ozhmenu li.ozhmenu_toplevel :last') doesn't work...
	if (!ozh_h) {ozh_h = last_li.top + 25 }
	if ( first_li.top < last_li.top ) {
		jQuery('#wphead').css('border-top-width', (ozh_h+4)+'px'); 
	}
}
jQuery(document).ready(function() {
	// Remove unnecessary links in the top right corner
	if (oam_adminmenu) {
		var ozhmenu_uselesslinks = jQuery('#user_info p').html();
		ozhmenu_uselesslinks = ozhmenu_uselesslinks.replace(/ \| <a href="http:\/\/codex.wordpress.org\/">Help<\/a>/i, '');
		ozhmenu_uselesslinks = ozhmenu_uselesslinks.replace(/ \| <a href="http:\/\/wordpress.org\/support\/">Forums<\/a>/i, '');
		jQuery('#user_info p').html(ozhmenu_uselesslinks);
		jQuery('#user_info').css('z-index','81');
		// Get and apply current menu colors
		var ozhmenu_bgcolor = jQuery("#wphead").css('background-color');
		var ozhmenu_color = jQuery('#dashmenu li a').css('color');
		jQuery('#ozhmenu li.ozhmenu_over').css('background-color', ozhmenu_bgcolor).css('color', ozhmenu_color);
		jQuery('#ozhmenu li .current').css('background-color', ozhmenu_bgcolor).css('color', ozhmenu_color);
		// Remove original menus (this is, actually, not needed, since the CSS should have taken care of this)
		jQuery('#sidemenu').hide();
		jQuery('#adminmenu').hide();
		jQuery('#dashmenu').hide();
		jQuery('#user_info').css('right','1em');
		if (oam_hidesubmenu) {
			jQuery('#wpwrap #submenu').html('');
		}
		// Make title header smaller (same comment as above)
		jQuery('#wphead #viewsite a').css('font-size','10px');
		jQuery('#wphead h1').css('font-size','25px');
		// jQueryfication of the Son of Suckerfish Drop Down Menu
		// Original at: http://www.htmldog.com/articles/suckerfish/dropdowns/
		jQuery('#ozhmenu li.ozhmenu_toplevel').each(function() {
			jQuery(this).mouseover(function(){
				jQuery(this).addClass('ozhmenu_over');
				if (jQuery.browser.msie) {ozhmenu_hide_selects(true);}
			}).mouseout(function(){
				jQuery(this).removeClass('ozhmenu_over');
				if (jQuery.browser.msie) {ozhmenu_hide_selects(false);}
			});
		});
		// Dynamically float submenu elements if there are too many
		jQuery('.ozhmenu_toplevel span').mouseover(
			function(){
				var menulength = jQuery(this).parent().parent().find('ul li').length;
				if (menulength >= oam_toomanypluygins) {
					jQuery(this).parent().parent().find('ul li').each(function(){
						jQuery(this).css('float', 'left');
					});
				}
			}
		);
		// Function to hide <select> elements (display bug with MSIE)
		function ozhmenu_hide_selects(hide) {
			var hidden = (hide) ? 'hidden' : 'visible';
			jQuery('select').css('visibility',hidden);
		}
		// Show our new menu
		jQuery('#ozhmenu').show();
		// Resize if needed
		if (oam_menuresize) {
			ozhmenu_resize();
			// Bind resize event		
			jQuery(window).resize(function(){
				ozhmenu_resize();
			});
		}
		// WPMU : behavior for the "All my blogs" link
		jQuery( function($) {
			var form = $( '#all-my-blogs' ).submit( function() { document.location = form.find( 'select' ).val(); return false;} );
			var tab = $('#all-my-blogs-tab a');
			var head = $('#wphead');
			$('.blog-picker-toggle').click( function() {
				form.toggle();
				tab.toggleClass( 'current' );
				return false;
			});
		} );
	}
})
