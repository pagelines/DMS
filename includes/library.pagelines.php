<?php
/**
 *  This file adds the main menu, free version must be under appearance, this file is NOT in the free version.
 *  @package PageLines DMS
 *  @since 1.0.1
 *
 */
add_action( 'pagelines_max_mem', create_function('',"@ini_set('memory_limit',WP_MAX_MEMORY_LIMIT);") );

function pagelines_add_admin_menus() {
	global $_pagelines_options_page_hook;
	global $_pagelines_ext_hook;
	global $_pagelines_special_hook;
	global $_pagelines_templates_hook;
	global $_pagelines_account_hook;


	$_pagelines_account_hook = pagelines_insert_menu( PL_MAIN_DASH, __( 'Dashboard', 'pagelines' ), 'edit_theme_options', PL_MAIN_DASH, 'pagelines_build_account_interface' );

	
	if(!pl_deprecate_v2()){

		 $_pagelines_options_page_hook = pagelines_insert_menu( PL_MAIN_DASH, __( 'Site Options', 'pagelines' ), 'edit_theme_options', 'pagelines', 'pagelines_build_option_interface' );

		$_pagelines_special_hook = pagelines_insert_menu( PL_MAIN_DASH, __( 'Page Options', 'pagelines' ), 'edit_theme_options', 'pagelines_special', 'pagelines_build_special' );

		$_pagelines_templates_hook = pagelines_insert_menu( PL_MAIN_DASH, __( "Drag <span class='spamp'>&amp;</span> Drop", 'pagelines' ), 'edit_theme_options', 'pagelines_templates', 'pagelines_build_templates_interface' );

		// $_pagelines_ext_hook = pagelines_insert_menu( PL_MAIN_DASH, __( 'Extend', 'pagelines' ), 'edit_theme_options', PL_ADMIN_STORE_SLUG, 'pagelines_build_extension_interface' );

	} else {
			
	}
}

/**
 *
 * PageLines menu wrapper
 */
function pagelines_insert_menu( $page_title, $menu_title, $capability, $menu_slug, $function ) {

	return add_submenu_page( PL_MAIN_DASH, $page_title, $menu_title, $capability, $menu_slug, $function );

}

/**
 * Full version menu wrapper.
 *
 */
function pagelines_add_admin_menu() {
		global $menu;

		// Create the new separator
		$menu['2.995'] = array( '', 'edit_theme_options', 'separator-pagelines', '', 'wp-menu-separator' );

		// Create the new top-level Menu
		add_menu_page( 'PageLines', 'PageLines', 'edit_theme_options', PL_MAIN_DASH, 'pagelines_build_account_interface', 'div', '2.996' );
}