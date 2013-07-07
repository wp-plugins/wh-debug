=== Plugin Name ===
Contributors: webheadllc
Tags: debug, development, developer tool
Requires at least: 3.4.1
Tested up to: 3.5.2
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides convenient methods for developers to debug buggy code.

== Description ==

This plugin is for developers.  Wherever you need to find out what is happening in your code place wh_debug('name', $value) in there.  Where 'name' describes what you are trying to debug and $value is the value you want to see.  Run your code and see the value(s) in a table on the admin side under Tools->WH Debug.

Development for this plugin is based on my needs and free time.

== Frequently Asked Questions ==

= wh_debug($name, $value) =

Adds $value to the options table with a 'wh_debug_' prefix.  Does not interrupt the normal flow of operations.

= wh_debug_hooks($hook_name, $echo=true) =

Print out or return all methods that will be called for $hook_name.  A hook can be a action or filter (ie 'wh_head').

= wh_debug_deactivate_plugin($plugin_path) and wh_debug_activate_plugin($plugin_path) =

Programmatically activate/deactivate a plugin.

= wh_debug_trace() =

Prints out all files leading up to the error.  Can ignore troublesome files/plugins.

== Changelog ==

= 1.0 =
Initial release.
