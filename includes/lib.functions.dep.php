<?php


/**
 *  Determines if on a foreign integration page
 *
 * @since 2.0.0
 */
function pl_is_integration(){
	
	_deprecated_function( __FUNCTION__, '1.1', 'integrations not supported' );
	
	global $pl_integration;

	return (isset($pl_integration) && $pl_integration) ? true : false;
}


/**
 *  returns the integration slug if viewing an integration page
 *
 * @since 2.0.0
 */
function pl_get_integration(){
	_deprecated_function( __FUNCTION__, '1.1', 'integrations not supported' );
	
	global $pl_integration;

	return (isset($pl_integration) && $pl_integration) ? sprintf('%s', $pl_integration) : false;
}

/**
 *
 * @TODO document
 *
 */
function pagelines_special_pages(){
	_deprecated_function( __FUNCTION__, '1.1', 'was used with failswith parameter which was deprecated' );
	return array('posts', 'search', 'archive', 'tag', 'category', '404');
}

/**
 * PageLines Background Cascade
 *
 * Sets background cascade for use in color mixing - default: White
 *
 * @since       2.0.b6
 *
 * @uses        ploption
 * @internal    uses filter background_cascade
 *
 * @return      mixed|void
 */
function pl_background_cascade(){
	_deprecated_function( __FUNCTION__, '1.1', 'css method not supported' );
	$cascade = array(
		ploption('contentbg'),
		ploption('pagebg'),
		ploption('bodybg'),
		'#ffffff'
	);

	return apply_filters('background_cascade', $cascade);
}

/**
 * PageLines Body Background
 *
 * Body Background - default: White
 *
 * @uses        ploption
 * @internal    uses filter body_bg
 *
 * @since       2.0.b6
 *
 * @return      mixed|void
 */
function pl_body_bg(){
_deprecated_function( __FUNCTION__, '1.1', 'css method not supported' );
	$cascade = array( ploption('bodybg'), '#ffffff' );

	return apply_filters('body_bg', $cascade);
}

/**
 * PageLines Add Page Callback
 *
 * Adds pages from the child theme.
 *
 * @since   1.1.0
 *
 * @param   $page_array
 * @param   $template_area
 *
 * @return  array
 */
function pagelines_add_page_callback( $page_array, $template_area ){
	_deprecated_function( __FUNCTION__, '1.1', 'page templates not supported' );
	global $pagelines_user_pages;

	if( is_array($pagelines_user_pages) ){
		foreach($pagelines_user_pages as $file => $pageinfo){
			$page_array[$file] = array('name'=>$pageinfo['name']);
		}
	}

	return $page_array;
}

/**
 * PageLines Nav Classes
 *
 * Returns nav menu class `sf-menu` which will allow the "superfish" JavaScript to work
 *
 * @package     PageLines Framework
 * @subpackage  Functions Library
 * @since       1.1.0
 *
 * @internal    see ..\sections\nav\script.superfish.js
 * @internal    see ..\sections\nav\style.superfish.css
 *
 * @return      string - CSS classes
 */
function pagelines_nav_classes(){

	_deprecated_function( __FUNCTION__, '1.1', 'old style navigation from PL Framework' );

	$additional_menu_classes = '';

	if(ploption('enable_drop_down'))
		$additional_menu_classes .= ' sf-menu';

	return $additional_menu_classes;
}

/**
 *
 *  Fallback for navigation, if it isn't set up
 *
 *  @package PageLines DMS
 *  @subpackage Functions Library
 *  @since 1.1.0
 *
 */

// DEPRECATED for pl_nav_fallback
function pagelines_nav_fallback() {
	
	_deprecated_function( __FUNCTION__, '1.1', 'old style navigation from PL Framework' );
	
	global $post; ?>

	<ul id="menu-nav" class="main-nav<?php echo pagelines_nav_classes();?>">
		<?php wp_list_pages( 'title_li=&sort_column=menu_order&depth=3'); ?>
	</ul><?php
}


