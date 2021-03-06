<?php
/*
Plugin Name: My Calendar
Plugin URI: http://www.joedolson.com/articles/my-calendar/
Description: Accessible WordPress event calendar plugin. Show events from multiple calendars on pages, in posts, or in widgets.
Author: Joseph C Dolson
Author URI: http://www.joedolson.com
Version: 1.8.8
*/
/*  Copyright 2009-2011  Joe Dolson (email : joe@joedolson.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
global $mc_version, $wpdb;
$mc_version = '1.8.8';

// Define the tables used in My Calendar
define('MY_CALENDAR_TABLE', $wpdb->prefix . 'my_calendar');
define('MY_CALENDAR_CATEGORIES_TABLE', $wpdb->prefix . 'my_calendar_categories');
define('MY_CALENDAR_LOCATIONS_TABLE', $wpdb->prefix . 'my_calendar_locations');

/*DRH*/
define('MY_CALENDAR_OCCURRENCES_TABLE', $wpdb->prefix.'my_calendar_occurrences');

// Define plugin constants
$my_calendar_directory = get_bloginfo( 'wpurl' ) . '/' . PLUGINDIR . '/' . dirname( plugin_basename(__FILE__) );
define( 'MY_CALENDAR_DIRECTORY', $my_calendar_directory );

include(dirname(__FILE__).'/my-calendar-core.php' );
include(dirname(__FILE__).'/my-calendar-settings.php' );
include(dirname(__FILE__).'/my-calendar-categories.php' );
include(dirname(__FILE__).'/my-calendar-locations.php' );
include(dirname(__FILE__).'/my-calendar-help.php' );
include(dirname(__FILE__).'/my-calendar-event-manager.php' );
include(dirname(__FILE__).'/my-calendar-styles.php' );
include(dirname(__FILE__).'/my-calendar-behaviors.php' );
include(dirname(__FILE__).'/my-calendar-widgets.php' );
include(dirname(__FILE__).'/date-utilities.php' );
include(dirname(__FILE__).'/my-calendar-install.php' );
include(dirname(__FILE__).'/my-calendar-upgrade-db.php' );
include(dirname(__FILE__).'/my-calendar-user.php' );
include(dirname(__FILE__).'/my-calendar-output.php' );
include(dirname(__FILE__).'/my-calendar-templates.php' );
include(dirname(__FILE__).'/my-calendar-rss.php' );
include(dirname(__FILE__).'/my-calendar-ical.php' );
include(dirname(__FILE__).'/my-calendar-events.php' );
include(dirname(__FILE__).'/my-calendar-limits.php' );
include(dirname(__FILE__).'/my-calendar-shortcodes.php' );
include(dirname(__FILE__).'/my-calendar-detect-mobile.php' );
//require_once( ABSPATH . WPINC . '/pluggable.php' );

// Install on activation
register_activation_hook( __FILE__, 'check_my_calendar' );

// Enable internationalisation
load_plugin_textdomain( 'my-calendar',false, dirname( plugin_basename( __FILE__ ) ) . '/lang' ); 

if ( version_compare( get_bloginfo( 'version' ) , '3.0' , '<' ) && is_ssl() ) {
	$wp_content_url = str_replace( 'http://' , 'https://' , get_option( 'siteurl' ) );
} else {
	$wp_content_url = get_option( 'siteurl' );
}
$wp_content_url .= '/wp-content';
$wp_content_dir = ABSPATH . 'wp-content';
if ( defined('WP_CONTENT_URL') ) {
	$wp_content_url = constant('WP_CONTENT_URL');
}
if ( defined('WP_CONTENT_DIR') ) {
	$wp_content_dir = constant('WP_CONTENT_DIR');
}
$wp_plugin_url = $wp_content_url . '/plugins';
$wp_plugin_dir = $wp_content_dir . '/plugins';
$wpmu_plugin_url = $wp_content_url . '/mu-plugins';
$wpmu_plugin_dir = $wp_content_dir . '/mu-plugins';


// Add actions
add_action( 'admin_menu', 'my_calendar_menu' );
add_action( 'wp_head', 'my_calendar_wp_head' );
add_action( 'delete_user', 'mc_deal_with_deleted_user' );
add_action( 'widgets_init', create_function('', 'return register_widget("my_calendar_today_widget");') );
add_action( 'widgets_init', create_function('', 'return register_widget("my_calendar_upcoming_widget");') );
add_action( 'widgets_init', create_function('', 'return register_widget("my_calendar_mini_widget");') );
add_action( 'show_user_profile', 'mc_user_profile' );
add_action( 'edit_user_profile', 'mc_user_profile' );
add_action( 'profile_update', 'mc_user_save_profile');
add_action( 'init', 'my_calendar_add_feed' );
add_action( 'admin_menu', 'my_calendar_add_javascript' );
add_action( 'init','my_calendar_add_display_javascript' );
add_action( 'wp_footer','my_calendar_calendar_javascript' );
add_action( 'wp_head','my_calendar_fouc' );

// Add filters 
add_filter( 'widget_text', 'do_shortcode', 9 );
add_filter('plugin_action_links', 'jd_calendar_plugin_action', -10, 2);

// produce admin support box
function jd_show_support_box() {
?>
<div class="resources">
<ul>
<li><a href="http://mywpworks.com/wp-plugin-guides/my-calendar-plugin-beginners-guide/" rel="external"><?php _e("Buy the Beginner's Guide",'my-calendar'); ?></a></li>
<li><a href="http://www.joedolson.com/articles/my-calendar/" rel="external"><?php _e("Get Support",'my-calendar'); ?></a></li>
<li><a href="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=my-calendar-help"><?php _e("My Calendar Help",'my-calendar'); ?></a></li>
<li><strong><a href="http://www.joedolson.com/donate.php" rel="external"><?php _e("Make a Donation",'my-calendar'); ?></a></strong></li>
<li><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<div>
<input type="hidden" name="cmd" value="_s-xclick" />
<input type="hidden" name="hosted_button_id" value="UZBQUG2LKKMRW" />
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" name="submit" alt="Donate!" />
<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
</div>
</form>
</li>
</ul>

</div>
<?php
}

// Function to deal with adding the calendar menus
function my_calendar_menu() {
  global $wpdb;
  check_my_calendar();
  $allowed_group = 'manage_options';
  $allowed_group = get_option('can_manage_events');
  $icon_path = get_option('siteurl').'/wp-content/plugins/'.basename(dirname(__FILE__)).'/images';
	if ( function_exists('add_object_page') ) {
		add_object_page(__('My Calendar','my-calendar'), __('My Calendar','my-calendar'), $allowed_group, 'my-calendar', 'edit_my_calendar',$icon_path.'/icon.png' );
	} else {  
		if ( function_exists('add_menu_page') ) {
			add_menu_page(__('My Calendar','my-calendar'), __('My Calendar','my-calendar'), $allowed_group, 'my-calendar', 'edit_my_calendar',$icon_path.'/icon.png' );
		}
	}
	if ( function_exists('add_submenu_page') ) {
		add_submenu_page('my-calendar', __('Add/Edit Events','my-calendar'), __('Add/Edit Events','my-calendar'), $allowed_group, 'my-calendar', 'edit_my_calendar');
		add_action( "admin_head", 'my_calendar_write_js' );		
		add_action( "admin_head", 'my_calendar_add_styles' );

		add_submenu_page('my-calendar', __('Manage Categories','my-calendar'), __('Manage Categories','my-calendar'), 'manage_options', 'my-calendar-categories', 'my_calendar_manage_categories');
		add_submenu_page('my-calendar', __('Manage Locations','my-calendar'), __('Manage Locations','my-calendar'), 'manage_options', 'my-calendar-locations', 'my_calendar_manage_locations');		
		add_submenu_page('my-calendar', __('Settings','my-calendar'), __('Settings','my-calendar'), 'manage_options', 'my-calendar-config', 'edit_my_calendar_config');
		add_submenu_page('my-calendar', __('Style Editor','my-calendar'), __('Style Editor','my-calendar'), 'manage_options', 'my-calendar-styles', 'edit_my_calendar_styles');
		add_submenu_page('my-calendar', __('Behavior Editor','my-calendar'), __('Behavior Editor','my-calendar'), 'manage_options', 'my-calendar-behaviors', 'edit_my_calendar_behaviors');		
		add_submenu_page('my-calendar', __('My Calendar Help','my-calendar'), __('Help','my-calendar'), 'manage_options', 'my-calendar-help', 'my_calendar_help');		
	}
}

// add shortcode interpreters
add_shortcode('my_calendar','my_calendar_insert');
add_shortcode('my_calendar_upcoming','my_calendar_insert_upcoming');
add_shortcode('my_calendar_today','my_calendar_insert_today');
add_shortcode('my_calendar_locations','my_calendar_locations');
add_shortcode('my_calendar_categories','my_calendar_categories');

?>
