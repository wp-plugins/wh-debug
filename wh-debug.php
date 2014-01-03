<?php 
/*
Plugin Name: WH Debug
Description: Provides an api to place debug statements in code and view it on the admin page
Author: Webhead LLC
Author URI: http://webheadcoder.com 
Version: 1.1
*/

/**
 * Use this to add debug statements to the options table with a 'wh_debug_' prefix.
 * Does not interupt the normal flow.
 * Usage:
 *    if(function_exists('wh_debug')){ wh_debug('somename', 'some debug statement');}
 */
function wh_debug($name, $value) {
	$time = current_time( 'timestamp' );
    update_option('wh_debug_' . $time . '_' . $name, $value);
}

/**
 * Use this to print out or return all methods that will be called for $hook_name.
 * hooks can be either actions or filters.
 */
function wh_debug_hooks($hook_name, $echo=true) {
	global $wp_filter;
	if (!$echo) {
		return $wp_filter[$hook_name];
	}
	print_r( $wp_filter[$hook_name]);	
}

/**
 * Deactivate a plugin programmatically.
 */
function wh_debug_deactivate_plugin($plugin_path) {
	wh_debug_toggle_plugin($plugin_path);
}

/**
 * Activate a plugin programmatically.
 */
function wh_debug_activate_plugin($plugin_path) {
	wh_debug_toggle_plugin($plugin_path, true);
}

/**
 * Deactivate or activate a plugin programmatically.
 */
function wh_debug_toggle_plugin($plugin_path, $activate = false) {
	if( !function_exists( 'is_plugin_active' ) )
		include_once( ABSPATH.'wp-admin/includes/plugin.php');

	if ($activate) {
		if (is_plugin_active($plugin_path)) {
			deactivate_plugins($plugin_path);
		}
	}
	else {
		activate_plugin($plugin_path);
	}
}


/**
 * Sometimes it's hard to locate what exactly caused the problem.  this will list all files leading up to the error.
 */
function wh_debug_trace() {
	if ((WP_DEBUG && _wh_debug_error_ignore_files(true)) || !WP_DEBUG) {
		echo 'The following caused the upcoming message.<br>';
		$trace = debug_backtrace();
		foreach ($trace as  $value) {
			if (isset($value['file'])) {
				echo '<b>' . $value['file'] . '</b> on line <b>' . $value['line'] . '</b><br>';
			}
		}
	}
}


/**
 * Print out a more helpful message to find the error
 */
if (WP_DEBUG) {
	add_filter('deprecated_argument_trigger_error', '_wh_debug_error_ignore_files');
    add_action('deprecated_argument_run', 'wh_debug_trace');
}

/**
 * Sometimes plugins are troublesome and not clean.  This ignores such files.
 * for example, uncomment the filter.
 */
function my_ignore_files($ignored_files) {
    $ignored_files[] = '/^' . preg_quote(WP_PLUGIN_DIR . '/cubepoints-buddypress-integration', '/') . '.*/';
    return $ignored_files;
}
//add_filter('wh_debug_error_ignore_files', 'my_ignore_files');

/**
 * If an error is about to be triggered, if it is in our ignore files list, return false.
 */
function _wh_debug_error_ignore_files($trigger_error) {
	$trace = debug_backtrace();
	$ignore_files = apply_filters('wh_debug_error_ignore_files', array());
	foreach ($trace as  $value) {
		if (isset($value['file'])) {
			foreach ($ignore_files as $pattern) {
				if (preg_match($pattern, $value['file'])) {
					return false;
				}
			}
		}
	}
	return $trigger_error;
}



/**
 * Add a menu item to the tools menu.
 */
function wh_debug_add_menu() {
	add_management_page( 'WH Debug', 'WH Debug', 'manage_options', 'wh-debug', 'wh_debug_output' );
}
add_action('admin_menu', 'wh_debug_add_menu');

function wh_debug_admin_enqueue($hook) {
    if( stripos($hook, 'wh-debug' ) === FALSE)
        return;
    wp_enqueue_style( 'wh_debug_style', plugins_url('/css/wh-debug.css', __FILE__) );
}
add_action( 'admin_enqueue_scripts', 'wh_debug_admin_enqueue' );

/**
 * Output the page.
 */
function wh_debug_output() {
	require_once(dirname(__FILE__) . '/wh-options-table.php');

	$wp_list_table = new WH_Debug_Options_Table();
	$wp_list_table->prepare_items();

	echo '<h2>WH Debug - Options Table</h2>';

	echo '<form method="post">'; //for search
	echo '<input type="hidden" name="page" value="wh-debug">';
	$wp_list_table->display();
	echo '</form>';
}

