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
	if (oam_adminmenu) {
		// Remove unnecessary links in the top right corner
		var ozhmenu_uselesslinks = jQuery('#user_info p').html();
		ozhmenu_uselesslinks = ozhmenu_uselesslinks.replace(/ \| <a href="http:\/\/codex.wordpress.org\/.*?">.*?<\/a>/i, ''); // remove any link from the codex
		ozhmenu_uselesslinks = ozhmenu_uselesslinks.replace(/ \| <a href="http:\/\/wordpress.org\/support\/">.*?<\/a>/i, '');
		jQuery('#user_info p').html(ozhmenu_uselesslinks);
		jQuery('#user_info').css('z-index','81');
		// Get and apply current menu colors
		var ozhmenu_bgcolor = jQuery("#wphead").css('background-color');
		var ozhmenu_color = jQuery('#dashmenu li a').css('color');
		jQuery('#ozhmenu li.ozhmenu_over').css('background-color', ozhmenu_bgcolor).css('color', ozhmenu_color);
		jQuery('#ozhmenu li .current').css('background-color', ozhmenu_bgcolor).css('color', ozhmenu_color);
		// Remove original menus (this is, actually, not needed, since the CSS should have taken care of this)
		jQuery('#sidemenu').remove();
		jQuery('#adminmenu').remove();
		jQuery('#dashmenu').remove();
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
		// Function to hide <select> elements (display bug with MSIE)
		function ozhmenu_hide_selects(hide) {
			var hidden = (hide) ? 'hidden' : 'visible';
			jQuery('select').css('visibility',hidden);
		}
		// Show our new menu
		jQuery('#ozhmenu').show();

		// Fluency conditional stuff. A few stuff disabled with this plugin.
		if (!oam_fluency) {
			// Resize menu if needed and bind the resize event
			if (!jQuery.browser.safari) { // Safari on Mac doesn't like this. Safari sucks to be honest.
				ozhmenu_resize(); 
				jQuery(window).resize(function(){ozhmenu_resize();});
			}
			
			// Dynamically float submenu elements if there are too many
			var menuresize = {};
			jQuery('.ozhmenu_toplevel span').mouseover(
				function(){
					var target = jQuery(this).parent().parent().attr('id');
					if (!target || menuresize[target]) return; // we've hovered a speech bubble, or we've already reworked this menu
					var menulength = jQuery('#'+target+' ul li').length;
					if (menulength > oam_toomanypluygins) {
						var maxw = 0;
						// float every item to the left and get the biggest size
						jQuery('#'+target+' ul li').each(function(){
							jQuery(this).css('float', 'left');
							maxw = Math.max(parseInt(jQuery(this).css('width')), maxw);
						});
						// Resize the whole submenu
						if (maxw) {
							var cols = parseInt(menulength / oam_toomanypluygins)+1;
							jQuery('#'+target+' ul li').each(function(){
								jQuery(this).css('width', maxw+'px');
							});
							// Give the submenu a width = (max item width)*number of columns + 20px between each column
							jQuery('#'+target+' ul').css('width', ( cols*maxw + (20*(cols-1)) )+'px');
						}
					}
					menuresize[target] = true;
				}
			);

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
