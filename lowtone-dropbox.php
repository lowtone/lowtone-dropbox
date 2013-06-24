<?php
/*
 * Plugin Name: Dropbox library
 * Plugin URI: http://wordpress.lowtone.nl/libs/google-picker
 * Plugin Type: lib
 * Description: Library for Google Picker.
 * Version: 1.0
 * Author: Lowtone <info@lowtone.nl>
 * Author URI: http://lowtone.nl
 * License: http://wordpress.lowtone.nl/license
 */

namespace lowtone\dropbox {

	use lowtone\content\packages\Package;

	// Includes
	
	if (!include_once WP_PLUGIN_DIR . "/lowtone-content/lowtone-content.php") 
		return trigger_error("Lowtone Content plugin is required", E_USER_ERROR) && false;

	Package::init(array(
			Package::INIT_PACKAGES => array("lowtone"),
			Package::INIT_MERGED_PATH => __NAMESPACE__,
			Package::INIT_SUCCESS => function() {

				// Load text domain
				
				if (!is_textdomain_loaded("lowtone_dropbox"))
					load_textdomain("lowtone_dropbox", __DIR__ . "/assets/languages/" . get_locale() . ".mo");

				// Enqueue script

				$__enqueuedScipt = false;

				$createEnqueueScript = function($check) use (&$__enqueuedScipt) {
					return function() use ($check, &$__enqueuedScipt) {
						if ($__enqueuedScipt)
							return;

						if (!($key = key()))
							return;

						if (!(defined($check) && constant($check)))
							return;

						echo sprintf('<script type="text/javascript" src="https://www.dropbox.com/static/api/1/dropins.js" id="dropboxjs" data-app-key="%s"></script>', $key);

						$__enqueuedScipt = true;
					};
				};

				add_action("admin_print_scripts", ($adminEnqueueScript = $createEnqueueScript("LOWTONE_DROPBOX_ADMIN_ENQUEUE_SCRIPT")), 0);
				add_action("admin_print_footer_scripts", $adminEnqueueScript, 0);
				add_action("wp_print_scripts", ($wpEnqueueScript = $createEnqueueScript("LOWTONE_DROPBOX_WP_ENQUEUE_SCRIPT")), 0);
				add_action("wp_print_footer_scripts", $wpEnqueueScript, 0);

			}
		));

	// Functions
	
	function key() {
		return get_option("_lowtone_dropbox_key") ?: NULL;
	}

	function enqueueScript() {
		return is_admin() ? adminEnqueueScript() : wpEnqueueScript();
	}

	function adminEnqueueScript() {
		if (defined("LOWTONE_DROPBOX_ADMIN_ENQUEUE_SCRIPT"))
			return true;

		return define("LOWTONE_DROPBOX_ADMIN_ENQUEUE_SCRIPT", true);
	}

	function wpEnqueueScript() {
		if (defined("LOWTONE_DROPBOX_WP_ENQUEUE_SCRIPT"))
			return true;
			
		return define("LOWTONE_DROPBOX_WP_ENQUEUE_SCRIPT", true);
	}

}