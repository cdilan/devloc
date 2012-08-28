<?php
/**
 * Plugin Name: Show/Hide Adminbar in Wordpress 3.1
 * Plugin URI:
 * Description: Provides a setting to show or hide the new admin bar in <strong>Wordpress 3.1</strong>
 * Version: 1.0.0
 * Author: H.-Peter Pfeufer
 * Author URI: http://blog.ppfeufer.de
 */

define('SH_ADMINBAR_VERSION', '1.0.0');

/**
 * Damit nicht ständig die DB angefragt wird.
 */
$sh_adminbar_options = get_option('sh-adminbar-options');
function sh_adminbar_get_option ($parameter = '') {
	global $sh_adminbar_options;

	if($parameter == '') {
		return $sh_adminbar_options;
	} else {
		return $sh_adminbar_options[$parameter];
	}
}

/**
 * Menüpunkt unter Einstellungen
 */
function sh_adminbar_options() {
	if(current_user_can('manage_options')) {
		add_options_page("Admin Bar", __('Admin Bar'), 8, __FILE__, 'sh_adminbar_options_page');
	}
}

/**
 * Wenn Adminbar NICHT angezeigt werden soll
 */
if(sh_adminbar_get_option('sh-adminbar-hide') == 1) {
	add_filter( 'show_admin_bar', '__return_false' );
}

function sh_adminbar_options_page() {
	?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br /></div>
		<h2><?php _e('Settings for Show/Hide Adminbar', 'show-hide-admin-bar'); ?></h2>
		<form method="post" action="options.php">
			<?php
			// New way of setting the fields, for WP 2.7 and newer
			if(function_exists('settings_fields')) {
				settings_fields('sh-adminbar-options');
			}
			?>
			<input type="hidden" name="sh-adminbar-options[sh-adminbar-pluginname]" id="sh-adminbar-options[sh-adminbar-pluginname]" value="Show/Hide Adminbar in Wordpress 3.1" />
			<input type="hidden" name="sh-adminbar-options[sh-adminbar-pluginversion]" id="sh-adminbar-options[sh-adminbar-pluginversion]" value="<?php echo SH_ADMINBAR_VERSION; ?>" />
			<table class="form-table">
				<tr>
					<th scope="row" valign="top"><?php _e('Hide Admin Bar', 'show-hide-admin-bar'); ?></th>
					<td>
						<div>
							<input type="checkbox" value="1" <?php if(sh_adminbar_get_option('sh-adminbar-hide') == '1') echo 'checked="checked"'; ?> name="sh-adminbar-options[sh-adminbar-hide]" id="sh-adminbar-options[sh-adminbar-hide]" />
						</div>
					</td>
				</tr>
			</table>
			<p class="submit"><input type="submit" name="Submit" value="<?php _e('Save Changes', 'show-hide-admin-bar'); ?>" /></p>
		</form>
	</div>
<?php
}

/**
 * Plugin initialisieren.
 *
 * Hier werden die Optionen gespeichert, welche in der Administration eingestellt werden.
 * Auch wird hier die Sprachdatei geladen, sofern eine vorhanden ist.
 */
function sh_adminbar_init() {
	if(function_exists('register_setting')) {
		register_setting('sh-adminbar-options', 'sh-adminbar-options');
	}

	/**
	 * Sprachdatei wählen
	 */
	if(function_exists('load_plugin_textdomain')) {
		load_plugin_textdomain('show-hide-admin-bar', false, dirname(plugin_basename( __FILE__ )) . '/languages/');
	}
}

/**
 * Festlegen was zu tun ist, bei Aktivierung des Plugins.
 */
function sh_adminbar_activate() {
	$sh_adminbar_add_options = array(
		'sh-adminbar-pluginname' => 'Show/Hide Adminbar in Wordpress 3.1',
		'sh-adminbar-pluginversion' => SH_ADMINBAR_VERSION,
		'sh-adminbar-hide' => '0',
	);

	if(is_array(get_option('sh-adminbar-options'))) {
		add_option('sh-adminbar-options', $sh_adminbar_add_options);
	} else {
		update_option('sh-adminbar-options', $sh_adminbar_add_options);
	}
}

/**
 * Nur wenn User auch der aktuell angemeldete Nutzer auch darf.
 */
if(is_admin()) {
	add_action('admin_menu', 'sh_adminbar_options');
	add_action('admin_init', 'sh_adminbar_init');
}

/**
 * Plugin aktivieren.
 */
register_activation_hook(__FILE__, 'sh_adminbar_activate');
?>