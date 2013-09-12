<?php
/**
 * Deprecated functions
 *
 * @author		Simon Prosser
 * @copyright	2011 PageLines
 */

/**
 * pagelines_register_section()
 *
 * @since 1.0
 * @deprecated 2.0
 * @deprecated Sections are now autoloaded and registered by the framework.
 */
function pagelines_register_section() {
	_deprecated_function( __FUNCTION__, '2.0', 'the CHILDTHEME/sections/ folder' );
	return;
}

/**
 * cmath()
 *
 * @since 1.0
 * @deprecated 2.0
 * @deprecated A more useful function name
 */
function cmath( $color ) {
	_deprecated_function( __FUNCTION__, '2.0', 'loadmath' );
	return new PageLinesColor( $color );
}

function pl_get_theme_data( $stylesheet = null, $header = 'Version') {

	if ( function_exists( 'wp_get_theme' ) ) {
		return wp_get_theme( basename( $stylesheet ) )->get( $header );
	} else {
		$data = get_theme_data( sprintf( '%s/themes/%s/style.css', WP_CONTENT_DIR, basename( $stylesheet ) ) );
		return $data[ $header ];
	}
}

function pl_get_themes() {

	if ( ! class_exists( 'WP_Theme' ) )
		return get_themes();

	$themes = wp_get_themes();

	foreach ( $themes as $key => $theme ) {
		$theme_data[$key] = array(
			'Name'			=> $theme->get('Name'),
			'URI'			=> $theme->display('ThemeURI', true, false),
			'Description'	=> $theme->display('Description', true, false),
			'Author'		=> $theme->display('Author', true, false),
			'Author Name'	=> $theme->display('Author', false),
			'Author URI'	=> $theme->display('AuthorURI', true, false),
			'Version'		=> $theme->get('Version'),
			'Template'		=> $theme->get('Template'),
			'Status'		=> $theme->get('Status'),
			'Tags'			=> $theme->get('Tags'),
			'Title'			=> $theme->get('Name'),
			'Template'		=> ( '' != $theme->display('Template', false, false) ) ? $theme->display('Template', false, false) : $key,
			'Stylesheet'	=> $key,
			'Stylesheet Files'	=> array(
				0 => sprintf( '%s/style.css' , $theme->get_stylesheet_directory() )
			)
		);
	}

	return $theme_data;
}

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




/**
 * Deprecated constants, removing after a couple of revision, this will ensure store products get time to update.
 *
 */
define( 'CORE_VERSION', get_theme_mod( 'pagelines_version' ) );
define( 'THEMENAME', 'PageLines' );
define( 'CHILD_URL', get_stylesheet_directory_uri() );
define( 'CHILD_IMAGES', CHILD_URL . '/images' );
define( 'CHILD_DIR', get_stylesheet_directory() );
define( 'SECTION_ROOT', get_template_directory_uri() . '/sections');