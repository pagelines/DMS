<?php

add_action( 'pagelines_hook_init', 'load_pagelines_admin' ); 
function load_pagelines_admin(){
			
	require_once( PL_ADMIN . '/admin.init.php' );
}

add_action( 'pagelines_hook_init', 'install_pagelines', 5 ); 
function install_pagelines(){
	
	if( class_exists('PageLinesInstallTheme') )
		new PageLinesInstallTheme;
	else
		new PageLinesInstall;
}

// Always best to load most stuff after WP loads fully.
// The "after_setup_theme" hook is the point at which it has... 
// NOTE: pl_setting cannot be used BEFORE the 'after_setup_theme' hook
add_action('after_setup_theme', 'pl_load_registers'); 
function pl_load_registers(){

	/**
	 * Load Singleton Globals
	 */
	$GLOBALS['pl_section_factory'] = new PageLinesSectionFactory();


	/**
	 * Add Extension Handlers
	 */
//	require_once( PL_INCLUDES . '/class.register.php' );
	global $editorsections;
	$editorsections->register_sections();
	pagelines_register_hook( 'pagelines_setup' ); // Hook

	$GLOBALS['render_css'] = new PageLinesRenderCSS;
	
	if ( pl_setting( 'enable_debug' ) )
		require_once ( PL_INCLUDES . '/class.debug.php');
}

/**
 *  Run persistent method of all sections in factory
 */
add_filter( 'pagelines_setup', 'load_section_persistent' );
function load_section_persistent( ){
	
	// Load persistent section functions (e.g. custom post types)
	global $pl_section_factory;
	foreach($pl_section_factory->sections as $section){
		$section->section_persistent();
	}
	
}

/**
 * Support optional WordPress functionality 'add_theme_support'
 */
add_action('after_setup_theme', 'pl_theme_support');
function pl_theme_support(  ){

	add_theme_support( 'post-thumbnails' );
	
	add_theme_support( 'post-formats', array('quote','video','audio','gallery','link') );
	
	add_image_size( 'aspect-thumb', 900, 600, true );
	add_image_size( 'basic-thumb', 400, 400, true );
	add_image_size( 'landscape-thumb', 900, 450, true );
	
	add_theme_support( 'menus' );
	add_theme_support( 'automatic-feed-links' );
	
	add_theme_support( 'woocommerce' );

}


add_action( 'template_redirect', 'pagelines_check_lessdev', 9 );
function pagelines_check_lessdev(){
	
	if( 1 == pl_setting( 'no_cache_mode' ) ) {
		PageLinesRenderCSS::flush_version( false );
		pl_flush_draft_caches();
		delete_transient( 'pagelines_sections_cache' );
		set_theme_mod( 'editor-sections-data', array() );
	}
}

/**
 * Auto load child less file.
 */
add_action( 'init', 'pagelines_check_child_less' );
function pagelines_check_child_less() {

	// we might be a new standalone theme so we need to check tamplatedir too now.
	if( defined( 'DMS_CORE' ) ) {		

		$lessfile = sprintf( '%s/style.less', get_template_directory() );
		if ( is_file( $lessfile ) )
			pagelines_insert_core_less( $lessfile );
	}
	// include child style.less
	$lessfile = sprintf( '%s/style.less', get_stylesheet_directory() );

	if ( is_file( $lessfile ) )
		pagelines_insert_core_less( $lessfile );
}



add_action( 'init', 'pagelines_check_less_reset', 999 );
function pagelines_check_less_reset() {

	if( isset( $_GET['pl_reset_less'] ) && ! defined( 'PL_CSS_FLUSH' ) )
		do_action( 'extend_flush' );

}



/**
 *  Fix The WordPress Login Image URL
 */
add_filter('login_headerurl', 'fix_wp_login_imageurl');
function fix_wp_login_imageurl( $url ){
	return home_url();
}

/**
 *  Fix The WordPress Login Image Title
 */
add_filter('login_headertitle', 'fix_wp_login_imagetitle');
function fix_wp_login_imagetitle( $url ){
	return get_bloginfo('name');
}

/**
 *  Fix The WordPress Login Image Title
 */
add_action('login_head', 'pl_fix_login_image');
function pl_fix_login_image( ){

	$image_url = (pl_setting('pl_login_image')) ? pl_setting('pl_login_image') : PL_IMAGES . '/default-login-image.png';

	$css = sprintf('body #login h1 a{background: url(%s) no-repeat top center;height: 80px; background-size:auto; width:auto;}', $image_url);
	
	inline_css_markup('pagelines-login-css', $css);
}