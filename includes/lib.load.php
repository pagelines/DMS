<?php


/**
 * Support optional WordPress functionality 'add_theme_support'
 */
add_action('after_setup_theme', 'pl_theme_support');
function pl_theme_support(  ){

	add_theme_support( 'post-thumbnails' );
	add_image_size( 'aspect-thumb', 900, 600, true );
	add_image_size( 'basic-thumb', 400, 400, true );
	add_image_size( 'landscape-thumb', 900, 450, true );
	
	add_theme_support( 'menus' );
	add_theme_support( 'automatic-feed-links' );
	
	add_theme_support( 'woocommerce' );

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

	$image_url = (ploption('pl_login_image')) ? ploption('pl_login_image') : PL_ADMIN_IMAGES . '/login-pl.png';

	$css = sprintf('body #login h1 a{background: url(%s) no-repeat top center;height: 80px;background-size:auto; background-size: auto 80px;}', $image_url);

	inline_css_markup('pagelines-login-css', $css);
}