<?php
/*
Plugin Name: CubePoints
Plugin URI: http://cubepoints.com
Description: CubePoints is a point management system designed for WordPress blogs. Users can earn points by posting comments on your site. To display user's points, just put <code>&lt;?php cp_displayPoints(); ?&gt;</code> in your template or activate the sidebar widget.
Version: 3.1.1
Author: Jonathan Lau & Peter Zhang
Author URI: http://cubepoints.com
*/

global $wpdb;

/** Define constants */
define('CP_VER', '3.1.1');
define('CP_DB', $wpdb->base_prefix . 'cp');
define('CP_PATH', WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));

/** Set CubePoints Version **/
add_option('cp_ver', CP_VER);

/** Loads the plugin's translated strings */
load_plugin_textdomain('cp', false, dirname(plugin_basename(__FILE__)).'/languages');

/** Includes commons */
require_once 'cp_common.php';

/** Includes install script */
require_once 'cp_install.php';

/** Includes upgrade script */
require_once 'cp_upgrade.php';

/** Includes core functions */
require_once 'cp_core.php';

/** Includes plugin hooks */
require_once 'cp_hooks.php';

/** Includes plugin APIs */
require_once 'cp_api.php';

/** Includes widgets */
require_once 'cp_widgets.php';

/** Includes logs display */
require_once 'cp_logs.php';

/** Includes admin pages */
require_once 'cp_admin.php';

/** Hook for plugin installation */
register_activation_hook( __FILE__ , 'cp_activate' );
function cp_activate(){
	cp_install();
}

/** Include all modules in the modules folder */
add_action('plugins_loaded','cp_modules_include',2);

/** Checks if modules have been updated and run activation hook */
add_action('init', 'cp_modules_updateCheck');

?>