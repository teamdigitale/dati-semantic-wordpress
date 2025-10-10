<?php
/**
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

/**
 * Get retention.json absolute path
 *
 * @param  array  $params Request parameters
 * @return string
 */
function ai1wmue_retention_path( $params ) {
	return ai1wm_storage_path( $params ) . DIRECTORY_SEPARATOR . AI1WMUE_RETENTION_NAME;
}

/**
 * Check whether export/import is running
 *
 * @return boolean
 */
function ai1wmue_is_running() {
	if ( isset( $_GET['file'] ) || isset( $_POST['file'] ) ) {
		return true;
	}

	return false;
}

/**
 * Check if the base plugin is installed
 *
 * @return boolean
 */
function ai1wmue_is_base_plugin_installed() {
	return file_exists( WP_PLUGIN_DIR . '/all-in-one-wp-migration/all-in-one-wp-migration.php' );
}

/**
 * Check if the base plugin is activated
 *
 * @return boolean
 */
function ai1wmue_is_base_plugin_active() {
	return is_plugin_active( 'all-in-one-wp-migration/all-in-one-wp-migration.php' );
}

/**
 * Install the base plugin from WordPress repository
 *
 * @return boolean|WP_Error
 */
function ai1wmue_install_base_plugin() {
	if ( ! function_exists( 'plugins_api' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	}

	if ( ! class_exists( 'WP_Upgrader', false ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	}

	$api = plugins_api(
		'plugin_information',
		array(
			'slug'   => 'all-in-one-wp-migration',
			'fields' => array(
				'short_description' => false,
				'sections'          => false,
				'requires'          => false,
				'rating'            => false,
				'ratings'           => false,
				'downloaded'        => false,
				'last_updated'      => false,
				'added'             => false,
				'tags'              => false,
				'compatibility'     => false,
				'homepage'          => false,
				'donate_link'       => false,
			),
		)
	);

	if ( is_wp_error( $api ) ) {
		return $api;
	}

	$upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
	$result   = $upgrader->install( $api->download_link );

	return $result;
}
