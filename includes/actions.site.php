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
add_filter( 'user_contactmethods', 'pagelines_add_google_profile', 10, 1);
function pagelines_add_google_profile( $contactmethods ) {
	// Add Google Profiles
	$contactmethods['google_profile'] = __( 'Google Profile URL', 'pagelines' );
	return $contactmethods;
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


// add_action( 'template_redirect', 'pl_check_integrations' ); // shouldnt be needed now

add_action( 'comment_form_before', 'pl_comment_form_js' );
function pl_comment_form_js() {
	if ( get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
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
