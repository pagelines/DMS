<?php

// Load account functions
$account_handler = new PLAccountAdmin;

// ====================================
// = Build PageLines Option Interface =
// ====================================

// Add our menus where they belong.
add_action( 'admin_menu', 'pagelines_add_admin_menu' );

add_action('admin_menu', 'pagelines_add_admin_menus');

if( ! function_exists( 'pagelines_add_admin_menu' ) ) {
	
	function pagelines_add_admin_menus() {}
	
	function pagelines_add_admin_menu() {
		global $_pagelines_account_hook;
		$_pagelines_account_hook = add_theme_page( PL_MAIN_DASH, __( 'DMS Tools', 'pagelines' ), 'edit_theme_options', PL_MAIN_DASH, 'pagelines_build_account_interface' );
	}
}

// Build option interface


/**
 * Build Extension Interface
 * Will handle adding additional sections, plugins, child themes
 */
function pagelines_build_account_interface(){
	
	$dms_tools = new EditorAdmin;

	$args = array(
		'title'			=> __( 'PageLines DMS', 'pagelines' ),
		'callback'		=> array( $dms_tools, 'admin_array' ),
	);
	$optionUI = new DMSOptionsUI( $args );
}

/**
 * This is a necessary go-between to get our scripts and css loaded
 * on the theme settings page only, and not the rest of the admin
 */
add_action( 'admin_menu', 'pagelines_theme_settings_init' );
function pagelines_theme_settings_init() {
	global $_pagelines_account_hook;
	
	add_action( "admin_print_scripts-{$_pagelines_account_hook}", 'pagelines_theme_settings_scripts' );
}



// JS/CSS
function pagelines_theme_settings_scripts() {

	
	wp_enqueue_script( 'pl-library', PL_PARENT_URL . '/editor/js/pl.library.js', array( 'jquery' ), pl_get_cache_key() );
	wp_enqueue_script( 'pagelines-admin', PL_JS . '/admin.pagelines.js', array( 'jquery', 'pl-library' ), pl_get_cache_key() );
	
	pl_enqueue_codemirror();

}

add_action('admin_head', 'add_global_admin_css');
function add_global_admin_css() {
?>
<style type="text/css">
	#toplevel_page_PageLines-Admin .wp-menu-image img{ max-width: 18px; }
	#toplevel_page_PageLines-Admin.current  .wp-menu-image img{ opacity: 1; }
</style>

<?php

}

/**
 * Setup Versions and flush caches.
 *
 * @package PageLines DMS
 * @since   2.2
 */
add_action( 'admin_init', 'pagelines_set_versions' );
function pagelines_set_versions() {

	set_theme_mod( 'pagelines_version', PL_CORE_VERSION );
	set_theme_mod( 'pagelines_child_version', pl_get_theme_data( get_stylesheet_directory(), 'Version' ) );
}

// make sure were running out of 'pagelines' folder.
add_action( 'admin_notices', 'pagelines_check_folders' );
function pagelines_check_folders() {
		
		if( defined( 'DMS_CORE' ) )
			return;
		$folder = basename( get_template_directory() );

		if( 'dms' == $folder )
			return;

		echo '<div class="updated">';
		printf( "<p><h3>Install Error!</h3><br />PageLines DMS must be installed in a folder called 'dms' to work with child themes and extensions.<br /><br />Current path: %s<br /></p>", get_template_directory() );
		echo '</div>';
}

add_action( 'activate_plugin', 'pagelines_purge_sections_cache' );
add_action( 'deactivate_plugin', 'pagelines_purge_sections_cache' );
add_action( 'upgrader_process_complete', 'pagelines_purge_sections_cache' );
add_action( 'after_switch_theme', 'pagelines_purge_sections_cache' );
add_action( 'save_post', 'pagelines_reset_pl_cache_key' );

function pagelines_reset_pl_cache_key() {
	$key = substr(uniqid(), -6);
	set_theme_mod( 'pl_cache_key', $key );
	return $key;
}
function pagelines_purge_sections_cache() {
	delete_transient( 'pagelines_sections_cache' );
	set_theme_mod( 'editor-sections-data', array() );
}

add_action('admin_enqueue_scripts', 'pagelines_metabox_scripts');
function pagelines_metabox_scripts() {
	wp_enqueue_style( 'pagelines-css', sprintf( '%s/admin.css', PL_ADMIN_URI ), null, pl_get_cache_key() );
	wp_enqueue_script( 'pagelines-admin-meta', PL_ADMIN_URI .'/admin.js', array('jquery'));
}

