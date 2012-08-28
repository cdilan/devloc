<?php

/*
  Plugin Name: Lanoba Social
  Plugin URI: http://www.lanoba.com/
  Description: Provides authentication, registration and social sharing capabilities through social networks.
  Version: 2.1.10
  Author: Lanoba
  Author URI: http://www.lanoba.com/
  License: GPL2
 */
require_once(dirname(__FILE__) . '/includes/admin.php');

//-------------------------------------------------------------------------------
function lanoba_action_links($links, $file)
{
    if ( $file != plugin_basename(__FILE__) )
     return $links;

    $settings_link = '<a href="options-general.php?page=LanobaWP">Settings</a>';

    array_unshift($links, $settings_link);

    return $links;
}

//------------------------------------------------------------------------------
function lb_PluginMenu()
{
    add_menu_page('Lanoba Social Plugin Settings', 'Lanoba Social', 'manage_options', 'LanobaWP', 'lb_PluginOptions', WP_PLUGIN_URL . "/lanoba-social/images/lanoba.gif");
}

//------------------------------------------------------------------------------
//the plugin will work function if cURL and add_function exist and the appropriate version of PHP is available.
$adminErrorMessage = "";

if ( version_compare(PHP_VERSION, '5.2.0', '<') )
{
    $adminErrorMessage .= "PHP 5.2 or newer not found!<br/>";
}

if ( !function_exists("curl_init") )
{
    $adminErrorMessage .= "cURL library was not found!<br/>";
}

if ( !function_exists("session_start") )
{
    $adminErrorMessage .= "Sessions are not enabled!<br/>";
}

if ( !function_exists("json_decode") )
{
    $adminErrorMessage .= "JSON was not enabled!<br/>";
}

if ( function_exists('add_action') && function_exists('add_filter') ):
    
// Admin and Settings links
    add_action('admin_print_styles', 'lb_enqueue_styles');
    add_filter('plugin_action_links', 'lanoba_action_links', 10, 2);
    add_action('admin_menu', 'lb_PluginMenu');

//the rest of the plugin
    if ( empty($adminErrorMessage) ):

        require_once(dirname(__FILE__) . '/includes/lanobawidget.php');
        require_once(dirname(__FILE__) . '/includes/users.php');

        if ( lb_getSetting('lb_apiSecret') && lb_getSetting('lb_hostName') ):
            add_action('init', 'lb_enqueue_js');
            add_action('register_post', 'lb_LanobaPost');
            add_action('register_post', 'lb_RegisterFormPost');
            add_action('register_form', 'lb_RegisterFormInit');
            add_action('show_user_profile', 'lb_ShowMapping');
            add_action('profile_update', 'lb_SaveProfile');
            add_action('wp_logout', 'lb_Logout');
            add_filter('login_errors', 'lb_LoginErrors');
          //  add_filter('the_content', 'lb_ShareWidget');
            add_action('widgets_init', create_function('', 'return register_widget( "LanobaSocialWidget" );'));

            lb_displayWidgets();

        endif;
    endif; 
endif; 