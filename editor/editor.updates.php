<?php
/**
 *  DMS 3rd party plugin/theme updating class.
 *
 *  @package DMS
 *  @since 1.0
 *
 *
 */
class PageLinesEditorUpdates {
	

	function __construct() {
		$this->register_core();
		add_action( 'init', array( $this, 'check_updater_status' ) );
			
	}

	function register_core() {
		
		// if we are parent theme OR a child theme, register as DMS.
		// if we are a standalone, register as the standalone.		
		$themeslug = $this->get_themeslug();
		
		global $registered_pagelines_updates;
		if( ! is_array( $registered_pagelines_updates ) )
			$registered_pagelines_updates = array();
		$registered_pagelines_updates['dmspro'] = array(
			'type' => 'theme',
			'product_file_path'	=> 'dmspro',
			'product_name'	=> __( 'PageLines Club Membership', 'pagelines' ),
			'product_version'	=> '',
			'product_desc'		=> __( 'Activating this will automatically give you updates to any PageLines Product detected.', 'pagelines' )
			);
						
		$registered_pagelines_updates[$themeslug] = array(
			'type' => 'theme',
			'product_file_path'	=> $themeslug,
			'product_name'	=> pl_get_theme_data( get_template_directory(), 'Name'),
			'product_version'	=> pl_get_theme_data( get_template_directory(), 'Version'),
			'product_desc'		=> pl_get_theme_data( get_template_directory(), 'Description')
			);		
	}

	function get_themeslug() {
		if( defined( 'DMS_CORE' ) )
			return basename( get_stylesheet_directory() );
		else
			return 'dms';
	}

	function check_updater_status() {
		
		// if not installed, show link to install.
		if( ! pl_check_updater_exists() ) {
			add_filter( 'plugins_api', array( $this, 'pagelines_updater_install' ), 10, 3 );			
		}
		// if installed but not activated, lets activate it.
		add_action( 'admin_notices', array( $this, 'updater_install' ) );
	}


	function pagelines_updater_install( $api, $action, $args ) {
		$cache = rand();
		$download_url = 'http://www.pagelines.com/api/store/plugin-pagelines-updater.zip?c=' . $cache;

		if ( 'plugin_information' != $action ||
			false !== $api ||
			! isset( $args->slug ) ||
			'pagelines-updater' != $args->slug
		) return $api;

		$api = new stdClass();
		$api->name = 'PageLines Updater';
		$api->version = '1.0.0';
		$api->download_link = esc_url( $download_url );
		return $api;
	}

	function updater_install() {
		
		//normal
		$active_plugins = apply_filters( 'active_plugins', get_option('active_plugins' ) );
		if ( in_array( 'pagelines-updater/pagelines-updater.php', $active_plugins ) )
			return;
		// ms
		if ( ! function_exists( 'is_plugin_active_for_network' ) )
		    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		
		if ( is_plugin_active_for_network( 'pagelines-updater/pagelines-updater.php' ) )
			return
		
		$slug = 'pagelines-updater';
		$install_url = wp_nonce_url( network_admin_url( 'update.php?action=install-plugin&plugin=pagelines-updater' ), 'install-plugin_pagelines-updater' );
		$activate_url = 'plugins.php?action=activate&plugin=' . urlencode( 'pagelines-updater/pagelines-updater.php' ) . '&plugin_status=all&paged=1&s&_wpnonce=' . urlencode( wp_create_nonce( 'activate-plugin_pagelines-updater/pagelines-updater.php' ) );

		$message = sprintf( '<a class="btn btn-mini" href="%s"> %s', esc_url( $install_url ), __( 'Install the PageLines Updater plugin</a> to activate a key and get updates for your PageLines themes and plugins.', 'pagelines' ) );
		$is_downloaded = false;
		$plugins = array_keys( get_plugins() );
		foreach ( $plugins as $plugin ) {
			if ( strpos( $plugin, 'pagelines-updater.php' ) !== false ) {
				$is_downloaded = true;
				$message = sprintf( '<a class="btn btn-mini" href="%s">%s', esc_url( network_admin_url( $activate_url ) ), __( 'Activate the PageLines Updater plugin</a> to activate your key and get updates for your PageLines themes and plugins.', 'pagelines' ) );
			}
		}
		echo '<div class="updated fade"><p>' . $message . '</p></div>' . "\n";
	}
}

new PageLinesEditorUpdates;