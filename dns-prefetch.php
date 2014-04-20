<?php
/*
Plugin Name: DNS Prefetch
Plugin URI: http://www.jimmyscode.com/wordpress/dns-prefetch/
Description: Add DNS prefetching meta tags to your site.
Version: 0.0.2
Author: Jimmy Pe&ntilde;a
Author URI: http://www.jimmyscode.com/
License: GPLv2 or later
*/

	define('DPF_PLUGIN_NAME', 'DNS Prefetch');
	// plugin constants
	define('DPF_VERSION', '0.0.2');
	define('DPF_SLUG', 'dns-prefetch');
	define('DPF_LOCAL', 'dpf');
	define('DPF_OPTION', 'dpf');
	define('DPF_OPTIONS_NAME', 'dpf_options');
	define('DPF_PERMISSIONS_LEVEL', 'manage_options');
	define('DPF_PATH', plugin_basename(dirname(__FILE__)));
	/* default values */
	define('DPF_DEFAULT_ENABLED', true);
	define('DPF_DEFAULT_TEXT', '');
	/* option array member names */
	define('DPF_DEFAULT_ENABLED_NAME', 'enabled');
	define('DPF_DEFAULT_TEXT_NAME', 'domainstoadd');
	
	// oh no you don't
	if (!defined('ABSPATH')) {
		wp_die(__('Do not access this file directly.', DPF_LOCAL));
	}

	// delete option when plugin is uninstalled
	register_uninstall_hook(__FILE__, 'uninstall_dpf_plugin');
	function uninstall_dpf_plugin() {
		delete_option(DPF_OPTION);
	}
	// localization to allow for translations
	add_action('init', 'dpf_translation_file');
	function dpf_translation_file() {
		$plugin_path = plugin_basename(dirname(__FILE__) . '/translations');
		load_plugin_textdomain(DPF_LOCAL, '', $plugin_path);
	}
	// tell WP that we are going to use new options
	// also, register the admin CSS file for later inclusion
	add_action('admin_init', 'dpf_options_init');
	function dpf_options_init() {
		register_setting(DPF_OPTIONS_NAME, DPF_OPTION, 'dpf_validation');
		register_dpf_admin_style();
	}
	// validation function
	function dpf_validation($input) {
		// sanitize textarea
		$input[DPF_DEFAULT_TEXT_NAME] = wp_kses($input[DPF_DEFAULT_TEXT_NAME], wp_kses_allowed_html('post'));
		return $input;
	} 

	// add Settings sub-menu
	add_action('admin_menu', 'dpf_plugin_menu');
	function dpf_plugin_menu() {
		add_options_page(DPF_PLUGIN_NAME, DPF_PLUGIN_NAME, DPF_PERMISSIONS_LEVEL, DPF_SLUG, 'dpf_page');
	}
	// plugin settings page
	// http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/
	function dpf_page() {
		// check perms
		if (!current_user_can(DPF_PERMISSIONS_LEVEL)) {
			wp_die(__('You do not have sufficient permission to access this page', DPF_LOCAL));
		}
		?>
		<div class="wrap">
			<h2 id="plugintitle"><img src="<?php echo plugins_url(plugin_basename(dirname(__FILE__) . '/images/globe.png')) ?>" title="" alt="" height="64" width="64" align="absmiddle" /> <?php echo DPF_PLUGIN_NAME; _e(' by ', DPF_LOCAL); ?><a href="http://www.jimmyscode.com/">Jimmy Pe&ntilde;a</a></h2>
			<div><?php _e('You are running plugin version', DPF_LOCAL); ?> <strong><?php echo DPF_VERSION; ?></strong>.</div>
			<form method="post" action="options.php">
			<?php settings_fields(DPF_OPTIONS_NAME); ?>
			<?php $options = dpf_getpluginoptions(); ?>
			<?php update_option(DPF_OPTION, $options); ?>
			<h3 id="settings"><img src="<?php echo plugins_url(plugin_basename(dirname(__FILE__) . '/images/settings.png')) ?>" title="" alt="" height="61" width="64" align="absmiddle" /> <?php _e('Plugin Settings', DPF_LOCAL); ?></h3>
				<?php submit_button(); ?>

				<table class="form-table" id="theme-options-wrap">
					<tr valign="top"><th scope="row"><strong><label title="<?php _e('Is plugin enabled? Uncheck this to turn it off temporarily.', DPF_LOCAL); ?>" for="<?php echo DPF_OPTION; ?>[<?php echo DPF_DEFAULT_ENABLED_NAME; ?>]"><?php _e('Plugin enabled?', DPF_LOCAL); ?></label></strong></th>
						<td><input type="checkbox" id="<?php echo DPF_OPTION; ?>[<?php echo DPF_DEFAULT_ENABLED_NAME; ?>]" name="<?php echo DPF_OPTION; ?>[<?php echo DPF_DEFAULT_ENABLED_NAME; ?>]" value="1" <?php checked('1', $options[DPF_DEFAULT_ENABLED_NAME]); ?> /></td>
					</tr>
					<tr valign="top"><td colspan="2"><?php _e('Is plugin enabled? Uncheck this to turn it off temporarily.', DPF_LOCAL); ?></td></tr>
					<tr valign="top"><th scope="row"><strong><label title="<?php _e('Enter URLs to be prefetched', DPF_LOCAL); ?>" for="<?php echo DPF_OPTION; ?>[<?php echo DPF_DEFAULT_TEXT_NAME; ?>]"><?php _e('Enter URLs to be prefetched', DPF_LOCAL); ?></label></strong></th>
						<td><textarea rows="12" cols="75" id="<?php echo DPF_OPTION; ?>[<?php echo DPF_DEFAULT_TEXT_NAME; ?>]" name="<?php echo DPF_OPTION; ?>[<?php echo DPF_DEFAULT_TEXT_NAME; ?>]"><?php echo $options[DPF_DEFAULT_TEXT_NAME]; ?></textarea></td>
					</tr>
					<tr valign="top"><td colspan="2"><?php _e('Type the URLs you want to be prefetched by visitors\' browsers. One URL per line. Include prefix (such as <strong>http://</strong>) <br /><strong>These domains will be prefetched in addition to the domains already linked on your pages.</strong>', DPF_LOCAL); ?></td></tr>
				</table>
				<?php submit_button(); ?>
			</form>
			<hr />
			<h3 id="support"><img src="<?php echo plugins_url(plugin_basename(dirname(__FILE__) . '/images/support.png')) ?>" title="" alt="" height="64" width="64" align="absmiddle" /> Support</h3>
				<div class="support">
				<?php echo '<a href="http://wordpress.org/extend/plugins/' . DPF_SLUG . '/">' . __('Documentation', DPF_LOCAL) . '</a> | ';
					echo '<a href="http://wordpress.org/plugins/' . DPF_SLUG . '/faq/">' . __('FAQ', DPF_LOCAL) . '</a><br />';
					_e('If you like this plugin, please ', DPF_LOCAL);
					echo '<a href="http://wordpress.org/support/view/plugin-reviews/' . DPF_SLUG . '/">';
					_e('rate it on WordPress.org', DPF_LOCAL);
					echo '</a> ';
					_e('and click the ', DPF_LOCAL);
					echo '<a href="http://wordpress.org/plugins/' . DPF_SLUG .  '/#compatibility">';
					_e('Works', DPF_LOCAL);
					echo '</a> ';
					_e('button. For support please visit the ', DPF_LOCAL);
					echo '<a href="http://wordpress.org/support/plugin/' . DPF_SLUG . '">';
					_e('forums', DPF_LOCAL);
					echo '</a>.';
				?>
				<br /><br />
				<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7EX9NB9TLFHVW"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate with PayPal" title="Donate with PayPal" width="92" height="26" /></a>
				<br /><br />
				<small><?php _e('Disclaimer: This plugin is not affiliated with or endorsed by Mozilla.', WPSB_LOCAL); ?></small>
				</div>
		</div>
		<?php }

	// main function and filter
	add_action('wp_head', 'dpf_prefetch');
	function dpf_prefetch() {
		$options = dpf_getpluginoptions();
		$enabled = $options[DPF_DEFAULT_ENABLED_NAME];
		
		if ($enabled) {
			// https://developer.mozilla.org/en-US/docs/Controlling_DNS_prefetching
			$result = '<meta http-equiv="x-dns-prefetch-control" content="on">';
		
			$tta = explode("\n", $options[DPF_DEFAULT_TEXT_NAME]);

			if (!empty($tta)) {
				foreach ($tta as $dpfdomain) {
					$result .= '<link rel="dns-prefetch" href="' . $dpfdomain . '" />';
				}
			}
			echo $result;
		} // end enabled check
	} // end function
	
	// show admin messages to plugin user
	add_action('admin_notices', 'dpf_showAdminMessages');
	function dpf_showAdminMessages() {
		// http://wptheming.com/2011/08/admin-notices-in-wordpress/
		global $pagenow;
		if (current_user_can(DPF_PERMISSIONS_LEVEL)) { // user has privilege
			if ($pagenow == 'options-general.php') { // we are on Settings menu
				if ($_GET['page'] == DPF_SLUG) { // we are on this plugin's settings page
					$options = dpf_getpluginoptions();
					if ($options != false) {
						$enabled = $options[DPF_DEFAULT_ENABLED_NAME];
						if (!$enabled) {
							echo '<div id="message" class="error">' . DPF_PLUGIN_NAME . ' ' . __('is currently disabled.', DPF_LOCAL) . '</div>';
						}
					}
				}
			} // end page check
		} // end privilege check
	} // end admin msgs function
	// enqueue admin CSS if we are on the plugin options page
	add_action('admin_head', 'insert_dpf_admin_css');
	function insert_dpf_admin_css() {
		global $pagenow;
		if (current_user_can(DPF_PERMISSIONS_LEVEL)) { // user has privilege
			if ($pagenow == 'options-general.php') { // we are on Settings menu
				if ($_GET['page'] == DPF_SLUG) { // we are on this plugin's settings page
					dpf_admin_styles();
				}
			}
		}
	}
	// add settings link on plugin page
	// http://bavotasan.com/2009/a-settings-link-for-your-wordpress-plugins/
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'dpf_plugin_settings_link');
	function dpf_plugin_settings_link($links) {
		$settings_link = '<a href="options-general.php?page=' . DPF_SLUG . '">' . __('Settings', DPF_LOCAL) . '</a>';
		array_unshift($links, $settings_link);
		return $links;
	}
	// http://wpengineer.com/1295/meta-links-for-wordpress-plugins/
	add_filter('plugin_row_meta', 'dpf_meta_links', 10, 2);
	function dpf_meta_links($links, $file) {
		$plugin = plugin_basename(__FILE__);
		// create link
		if ($file == $plugin) {
			$links = array_merge($links,
				array(
					'<a href="http://wordpress.org/support/plugin/' . DPF_SLUG . '">' . __('Support', DPF_LOCAL) . '</a>',
					'<a href="http://wordpress.org/extend/plugins/' . DPF_SLUG . '/">' . __('Documentation', DPF_LOCAL) . '</a>',
					'<a href="http://wordpress.org/plugins/' . DPF_SLUG . '/faq/">' . __('FAQ', DPF_LOCAL) . '</a>'
			));
		}
		return $links;
	}
	// enqueue/register the admin CSS file
	function dpf_admin_styles() {
		wp_enqueue_style('dpf_admin_style');
	}
	function register_dpf_admin_style() {
		wp_register_style('dpf_admin_style',
			plugins_url(DPF_PATH . '/css/admin.css'),
			array(),
			DPF_VERSION . "_" . date('njYHis', filemtime(dirname(__FILE__) . '/css/admin.css')),
			'all');
	}
	// when plugin is activated, create options array and populate with defaults
	register_activation_hook(__FILE__, 'dpf_activate');
	function dpf_activate() {
		$options = dpf_getpluginoptions();
		update_option(DPF_OPTION, $options);
	}
	// generic function that returns plugin options from DB
	// if option does not exist, returns plugin defaults
	function dpf_getpluginoptions() {
		return get_option(DPF_OPTION, 
			array(
				DPF_DEFAULT_ENABLED_NAME => DPF_DEFAULT_ENABLED, 
				DPF_DEFAULT_TEXT_NAME => DPF_DEFAULT_TEXT
			));
	}
?>