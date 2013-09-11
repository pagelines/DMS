<?php

global $pagelines_template;

// ===================================================================================================
// = Set up Section loading & create pagelines_template global in page (give access to conditionals) =
// ===================================================================================================

/**
 * Build PageLines Template Global (Singleton)
 *
 * Must be built inside the page (wp_head) so conditionals can be used to identify the template
 * in the admin; the template does not need to be identified so it is loaded in the init action
 *
 * @global  object $pagelines_template
 * @since   1.0.0
 */
add_action('pagelines_before_html', 'build_pagelines_template');

/**
 * Build the template in the admin... doesn't need to load in the page
 * @since 1.0.0
 */
add_action('admin_head', 'build_pagelines_template', 5);

add_action('pagelines_before_html', 'build_pagelines_layout', 5);
add_action('admin_head', 'build_pagelines_layout');



add_filter( 'pagelines_options_array', 'pagelines_merge_addon_options' );



add_action('wp_print_styles', 'workaround_pagelines_template_styles'); // Used as workaround on WP login page (and other pages with wp_print_styles and no wp_head/pagelines_before_html)

add_action( 'wp_print_styles', 'pagelines_get_childcss', 99);


/**
 * Creates a global page ID for reference in editing and meta options (no unset warnings)
 *
 * @since 1.0.0
 */
add_action('pagelines_before_html', 'pagelines_id_setup', 5);


/**
 * Adds page templates from the child theme.
 *
 * @since 1.0.0
 */
add_filter('the_sub_templates', 'pagelines_add_page_callback', 10, 2);

/**
 * Adds link to admin bar
 *
 * @since 1.0.0
 */
add_action( 'admin_bar_menu', 'pagelines_settings_menu_link', 100 );

// ================
// = HEAD ACTIONS =
// ================

/**
 * Add Main PageLines Header Information
 *
 * @since 1.3.3
 */
add_action('pagelines_head', 'pagelines_head_common');

/**
 *
 * @TODO document
 *
 */
function pagelines_add_google_profile( $contactmethods ) {
	// Add Google Profiles
	$contactmethods['google_profile'] = __( 'Google Profile URL', 'pagelines' );
	return $contactmethods;
}
add_filter( 'user_contactmethods', 'pagelines_add_google_profile', 10, 1);

/**
 * ng gallery fix.
 *
 * @return gallery template path
 *
 */

add_filter( 'ngg_render_template', 'gallery_filter' , 10, 2);

/**
 * Auto load child less file.
 */
add_action( 'init', 'pagelines_check_child_less' );
function pagelines_check_child_less() {

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
 *
 * @TODO document
 *
 */
function gallery_filter( $a, $template_name) {

	if ( $template_name == 'gallery-plcarousel')
		return sprintf( '%s/carousel/gallery-plcarousel.php', PL_SECTIONS);
	else
		return $a;
}



// add_action( 'template_redirect', 'pl_check_integrations' ); // shouldnt be needed now

add_action( 'comment_form_before', 'pl_comment_form_js' );
function pl_comment_form_js() {
	if ( get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
}

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


add_action( 'template_redirect', 'pagelines_check_lessdev', 9 );
function pagelines_check_lessdev(){
	if ( ! isset( $_GET['pagedraft'] )
		&& defined( 'PL_LESS_DEV' )
		&& PL_LESS_DEV
		&& false == EditorLessHandler::is_draft()
		) {
		PageLinesRenderCSS::flush_version( false );
	}
}



add_action( 'wp_head', 'pagelines_google_author_head' );

function pagelines_google_author_head() {
	global $post;
	if( ! is_page() && ! is_single() && ! is_author() )
		return;
	$google_profile = get_the_author_meta( 'google_profile', $post->post_author );
	if ( '' != $google_profile )
		printf( '<link rel="author" href="%s" />%s', $google_profile, "\n" );
}


/**
 * Fixed element area at top of site page.
 *
 **/
add_action('pagelines_site_wrap', 'pl_fixed_top_area');
function pl_fixed_top_area(){
	?>
	<div id="fixed-top" class="pl-fixed-top" data-region="fixed-top">
		<?php pagelines_template_area('pagelines_fixed_top', 'fixed_top'); // Hook ?>
	</div>
	<div class="fixed-top-pusher"></div>
	<script> jQuery('.fixed-top-pusher').height( jQuery('.pl-fixed-top').height() ) </script>
	
	<?php 
}

/**
 *  Fix The WordPress Login Image Title
 */
if ( VPRO )
	add_action('login_head', 'pl_fix_login_image');
