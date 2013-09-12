<?php





/**
 * Register website Javascript
 */
add_action( 'wp_enqueue_scripts', 'pagelines_register_js' );
function pagelines_register_js() {

	wp_enqueue_script( 'pagelines-bootstrap-all', PL_JS . '/script.bootstrap.min.js', array( 'jquery' ), '2.2.2', true );
	wp_enqueue_script( 'pagelines-resizer', PL_JS . '/script.resize.js', array( 'jquery' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'pagelines-viewport', PL_JS . '/script.viewport.js', array( 'jquery' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'pagelines-waypoints', PL_JS . '/script.waypoints.js', array( 'jquery' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'pagelines-easing', PL_JS . '/script.easing.js', array( 'jquery' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'pagelines-fitvids', PL_JS . '/script.fitvids.js', array( 'jquery' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'pagelines-parallax', PL_JS . '/parallax.js', array( 'jquery' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'pagelines-common', PL_JS . '/pl.common.js', array( 'jquery' ), PL_CORE_VERSION, true );

	// Load Supersize BG Script
	pagelines_supersize_bg();
}

add_action( 'wp_print_styles', 'pagelines_get_childcss', 99);
function pagelines_get_childcss() {
	if ( ! is_admin() && is_child_theme() ){
		wp_enqueue_style( 'DMS-theme', get_bloginfo('stylesheet_url'), array(), pagelines_get_style_ver(), 'all');
	}
}
