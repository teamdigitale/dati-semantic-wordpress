<?php
/**
 * Plugin Name: All-in-One WP Migration Unlimited Extension
 * Plugin URI: https://servmask.com/
 * Description: Extension for All-in-One WP Migration that enables unlimited size exports and imports
 * Author: ServMask
 * Author URI: https://servmask.com/
 * Version: 2.79
 * Text Domain: all-in-one-wp-migration-unlimited-extension
 * Domain Path: /languages
 * Network: True
 * License: GPLv3
 *
 * Copyright (C) 2014-2025 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Attribution: This code is part of the All-in-One WP Migration plugin, developed by
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}

if ( is_multisite() ) {
	// Multisite Extension shall be used instead
	return;
}

// Check SSL Mode
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && ( $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) ) {
	$_SERVER['HTTPS'] = 'on';
}

// Plugin Basename
define( 'AI1WMUE_PLUGIN_BASENAME', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );

// Plugin Path
define( 'AI1WMUE_PATH', dirname( __FILE__ ) );

// Plugin URL
define( 'AI1WMUE_URL', plugins_url( '', AI1WMUE_PLUGIN_BASENAME ) );

// Include constants
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'constants.php';

// Include functions
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'functions.php';

// Include loader
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'loader.php';

// Register activation hook to install and activate base plugin if needed
register_activation_hook( __FILE__, 'ai1wmue_activate_plugin' );

/**
 * Plugin activation hook
 *
 * @return void
 */
function ai1wmue_activate_plugin() {
	// Check if the base plugin is installed
	if ( ! ai1wmue_is_base_plugin_installed() ) {
		// Install the base plugin
		$install_result = ai1wmue_install_base_plugin();

		if ( is_wp_error( $install_result ) ) {
			// Installation failed, deactivate this plugin
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die(
				sprintf(
					__( 'The All-in-One WP Migration plugin could not be installed automatically. Please <a href="%s" target="_blank">download and install it manually</a> before activating this extension.', AI1WMUE_PLUGIN_NAME ),
					'https://wordpress.org/plugins/all-in-one-wp-migration/'
				)
			);
		}
	}

	// Activate the base plugin if it's not already active
	if ( ! ai1wmue_is_base_plugin_active() ) {
		if ( ! function_exists( 'activate_plugin' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$activate_result = activate_plugin( 'all-in-one-wp-migration/all-in-one-wp-migration.php' );

		if ( is_wp_error( $activate_result ) ) {
			// Activation failed, deactivate this plugin
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die(
				sprintf(
					__( 'The All-in-One WP Migration plugin could not be activated automatically. Please <a href="%s">activate it manually</a> before activating this extension.', AI1WMUE_PLUGIN_NAME ),
					admin_url( 'plugins.php' )
				)
			);
		}
	}
}

// ===========================================================================
// = All app initialization is done in Ai1wmue_Main_Controller __constructor =
// ===========================================================================
$main_controller = new Ai1wmue_Main_Controller( 'AI1WMUE', 'file' );
