<?php 


/**
 * PageLines Body Classes
 *
 * Sets up classes for controlling design and layout and is used on the body tag
 *
 */
function pagelines_body_classes(){

	global $pagelines_template;
	global $pagelines_addclasses;

	$special_body_class = (ploption('special_body_class')) ? ploption('special_body_class') : '';

	
	$classes = array();
	
	$classes[] = $special_body_class;
	// child theme name
	$classes[] = sanitize_html_class( strtolower( PL_CHILDTHEMENAME ) );
	// pro
	$classes[] = (pl_is_pro()) ? 'pl-pro-version' : 'pl-basic-version';
	
	// for backwards compatiblity, dms is:
	$classes[] = 'responsive';
	$classes[] = 'full_width';

	// externally added via global variable (string)
	if ( isset( $pagelines_addclasses ) && $pagelines_addclasses )
		$classes = array_merge( $classes, (array) explode( ' ', $pagelines_addclasses ) );

	// ensure no duplicates or empties
	$classes = array_unique( array_filter( $classes ) );
	// filter & convert to string
	$body_classes = join(' ', (array) apply_filters('pagelines_body_classes', $classes) );

	return $body_classes;
}


/**
 * Adds classes to body
 */
function pagelines_add_bodyclass( $class ) {

	global $pagelines_addclasses;

	if ( !isset( $pagelines_addclasses ) )
		$pagelines_addclasses = '';

	if ( isset( $class ) )
		$pagelines_addclasses .= sprintf( ' %s', $class );

}
