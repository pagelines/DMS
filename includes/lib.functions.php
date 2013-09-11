<?php

// ==============================
// = PageLines Function Library =
// ==============================

// Deprecated Functions
require_once( PL_INCLUDES . '/lib.functions.dep.php' );

/**
 *  Determines if this page is showing several posts.
 *
 * @since 2.0.0
 */
function pagelines_is_posts_page(){
	if(is_home() || is_search() || is_archive() || is_category() || is_tag()) return true;
	else return false;
}


/**
 *
 * @TODO document
 *
 */
function pagelines_non_meta_data_page(){
	if(pagelines_is_posts_page() || is_404()) return true;
	else return false;
}


/**
 * is_pagelines_special() REVISED
 */
function is_pagelines_special( $args = array() ) {

	if ( is_404() || is_home() || is_search() || is_archive() )
		return true;
	else
		return false;
}



/**
 *
 *  Sets up global post ID and $post global for handling, reference and consistency
 *
 *  @package PageLines DMS
 *  @subpackage Functions Library
 *  @since 1.0.0
 *
 */
function pagelines_id_setup(){
	global $post;
	global $pagelines_ID;
	global $pagelines_post;

	if(isset($post) && is_object($post)){
		$pagelines_ID = $post->ID;
		$pagelines_post = $post;
	}
	else {
		$pagelines_post = '';
		$pagelines_ID = '';
	}

}

/**
 * PageLines Register Hook
 *
 * Stores for reference or use elsewhere.
 *
 * @package     PageLines Framework
 * @subpackage  Functions Library
 *
 * @since       1.3.3
 *
 * @link        http://www.pagelines.com/wiki/Pagelines_register_hook
 *
 * @param       $hook_name
 * @param       null $hook_area_id
 */
function pagelines_register_hook( $hook_name, $hook_area_id = null){

	/** Do The Hook	*/
	do_action( $hook_name, $hook_name, $hook_area_id);

}

/**
 * PageLines Template Area
 *
 * Does hooks for template areas
 *
 * @package     PageLines Framework
 * @subpackage  Functions Library
 *
 * @since       1.3.3
 *
 * @link        http://www.pagelines.com/wiki/Pagelines_template_area
 *
 * @param       $hook_name
 * @param       null $hook_area_id
 */
function pagelines_template_area( $hook_name, $hook_area_id = null){

	/** Do The Hook	*/
	do_action( $hook_name, $hook_area_id);

}


/**
 * PageLines Strip
 *
 * Strips White Space
 *
 * @since   2.0.b13
 *
 * @param   string $t - input string
 * @return  mixed
 */
function plstrip( $t ){

	if( is_pl_debug() )
		return $t;

	return preg_replace( '/\s+/', ' ', $t );
}


/**
 * Show Posts Nave
 *
 * Checks to see if there is more than one page for nav.
 *
 * @since 1.0.0
 *
 * @return bool
 * @TODO does this add a query?
 */
function show_posts_nav() {
	global $wp_query;
	return ($wp_query->max_num_pages > 1);
}


/**
 * Displays query information in footer (For testing - NOT FOR PRODUCTION)
 * @since 4.0.0
 */
function show_query_analysis(){
	if (current_user_can('administrator')){
	    global $wpdb;
	    echo '<pre>';
	    print_r($wpdb->queries);
	    echo '</pre>';
	}
}


/**
 * Custom Trim Excerpt
 *
 * Returns the excerpt at a user-defined length
 *
 * @param   $text - input text
 * @param   $length - number of words
 *
 * @return  string - concatenated with an ellipsis
 */
function custom_trim_excerpt($text, $length) {

	$text = strip_shortcodes( $text ); // optional
	$text = strip_tags($text);

	$words = explode(' ', $text, $length + 1);
	if ( count($words) > $length) {
		array_pop($words);
		$text = implode(' ', $words);
	}
	return ($text != '') ? $text.'&nbsp;<span class="hellip">[&hellip;]</span>' : '';
}


/**
 *
 * @TODO document
 *
 */
function pagelines_add_page($file, $name){
	global $pagelines_user_pages;

	$pagelines_user_pages[$file] = array('name' => $name);

}


/**
 * Setup PageLines Template
 *
 * Includes the loading template that sets up all PageLines templates
 *
 */
function setup_pagelines_template() {


	// if not true, then a non pagelines template is being rendered (wrap with .content)
	$GLOBALS['pagelines_render'] = true;

	get_header();

	if(!has_action('override_pagelines_body_output'))
		pagelines_template_area('pagelines_template', 'templates');

	get_footer();
}



/**
 * Overrides default excerpt handling so we have more control
 *
 * @since 1.2.4
 */
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'improved_trim_excerpt');
function improved_trim_excerpt($text) {

	// Group options at top :)
	global $ex_length, $ex_tags;

	if(pl_has_editor()){
		$allowed_tags = ( isset( $ex_tags ) && '' != $ex_tags ) ? $ex_tags : '';
		$excerpt_len = ( isset( $ex_length ) && '' != $ex_length ) ? $ex_length : 55;
	} else {
		$allowed_tags = (ploption('excerpt_tags')) ? ploption('excerpt_tags') : '';
		$excerpt_len = (ploption('excerpt_len')) ? ploption('excerpt_len') : 55;
	}

	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');

		$text = strip_shortcodes( $text );


		$text = apply_filters('the_content', $text);

		$text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text); // PageLines - Strip JS

		$text = str_replace(']]>', ']]&gt;', $text);

		$text = strip_tags($text, $allowed_tags); // PageLines - allow more tags


		$excerpt_length = apply_filters('excerpt_length', $excerpt_len );
		$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');

		$words = preg_split('/[\n\r\t ]+/', $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);

		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $excerpt_more;
		} else
			$text = implode(' ', $words);

	}
	return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}

/**
 * Generates an really short excerpt from the content or postid for tweets, facebook etc
 *
 * @param int|object $post_or_id can be the post ID, or the actual $post object itself
 * @param int $words the amount of words to allow
 * @param string $excerpt_more the text that is applied to the end of the excerpt if we algorithically snip it
 * @return string the snipped excerpt or the manual excerpt if it exists
 */
function pl_short_excerpt($post_or_id, $words = 10, $excerpt_more = ' [...]') {

	if ( is_object( $post_or_id ) )
		$postObj = $post_or_id;
	else $postObj = get_post($post_or_id);

	$raw_excerpt = $text = $postObj->post_excerpt;
	if ( '' == $text ) {
		$text = $postObj->post_content;

		$text = strip_shortcodes( $text );

		$text = sanitize_text_field( $text );

		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		$excerpt_length = $words;

		$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $excerpt_more;
		} else {
			$text = implode(' ', $words);
		}
	}
	return $text;
}




/**
 *
 *  Loads Special PageLines CSS Files, Optimized
 *
 *  @package PageLines DMS
 *  @subpackage Functions Library
 *  @since 1.2.0
 *
 */
function pagelines_draw_css( $css_url, $id = '', $enqueue = false){
	echo '<link rel="stylesheet" href="'.$css_url.'" />'."\n";
}


/**
 *
 *  Abstracts the Enqueue of Stylesheets, fixes bbPress issues with dropping hooks
 *
 *  @package PageLines DMS
 *  @since 1.3.0
 *
 */
function pagelines_load_css( $css_url, $id, $hash = PL_CORE_VERSION, $enqueue = true){

	wp_register_style($id, $css_url, array(), $hash, 'all');
    wp_enqueue_style( $id );
}

/**
 *
 *  Loading CSS using relative path to theme root. This allows dynamic versioning, overriding in child theme
 *
 *  @package PageLines DMS
 *  @since 1.4.0
 *
 */
function pagelines_load_css_relative( $relative_style_url, $id){

	$rurl = '/' . $relative_style_url;

	if( is_file(get_stylesheet_directory() . $rurl ) ){

		$cache_ver = pl_cache_version( get_stylesheet_directory() . $rurl );

		pagelines_load_css( PL_CHILD_URL . $rurl , $id, $cache_ver);

	} elseif(is_file(get_template_directory() . $rurl) ){

		$cache_ver = pl_cache_version( get_template_directory() . $rurl );

		pagelines_load_css( PL_PARENT_URL . $rurl , $id, $cache_ver);

	}


}

/**
 *
 * Get cache version number
 *
 *
 */
function pl_cache_version( $path, $version = PL_CORE_VERSION ){
	$date_modified = filemtime( $path );
	$cache_ver = str_replace('.', '', $version) . '-' . date('mdGis', $date_modified);

	return $cache_ver;
}

/**
 *
 *  Get Stylesheet Version
 *
 *  @package PageLines DMS
 *  @since 1.4.0
 *
 */
function pagelines_get_style_ver( $tpath = false ){

	// Get cache number that accounts for edits to base.css or style.css
	if( is_file(get_stylesheet_directory() .'/base.css') && !$tpath ){
		$date_modified = filemtime( get_stylesheet_directory() .'/base.css' );
		$cache_ver = str_replace('.', '', PL_CHILD_VERSION) . '-' . date('mdGis', $date_modified);
	} elseif(is_file(get_stylesheet_directory() .'/style.css') && !$tpath ){
		$date_modified = filemtime( get_stylesheet_directory() .'/style.css' );
		$cache_ver = str_replace('.', '', PL_CORE_VERSION) .'-'.date('mdGis', $date_modified);
	} elseif(is_file(get_template_directory() .'/style.css')){
		$date_modified = filemtime( get_template_directory() .'/style.css' );
		$cache_ver = str_replace('.', '', PL_CORE_VERSION) .'-'.date('mdGis', $date_modified);
	} else {
		$cache_ver = PL_CORE_VERSION;
	}


	return $cache_ver;

}

/**
 * Debugging, prints nice array.
 * Sends to the footer in all cases.
 *
 * @since 1.5.0
 */
function plprint( $data, $title = false, $echo = false) {

	if ( ! is_pl_debug() || ! current_user_can('manage_options') )
		return;

	ob_start();

		echo '<pre class="plprint">';

		if ( $title )
			printf('<h3>%s</h3>', $title);

		echo esc_html( print_r( $data, true ) );

		echo '</pre>';

	$data = ob_get_clean();

	if ( $echo )
		echo $data;
	elseif ( false === $echo )
		add_action( 'shutdown', create_function( '', sprintf('echo \'%s\';', $data) ) );
	else
		return $data;
}

function plcomment( $data, $title = 'DEBUG', $type = 'html' ) {


	if( is_pl_debug() ){
	$open	= ( 'html' == $type ) ? "\n<!-- " : "\n/* ";
	$close	= ( 'html' == $type ) ? " -->\n" : "*/\n";

	$pre = sprintf( '%s START %s %s', $open, $title, $close );
	$post = sprintf( '%s END %s %s', $open, $title, $close );

	return $pre . $data . $post;

	} else {
		return $data;
	}
}

/**
 * Creates Upload Folders for PageLines stuff
 *
 * @return true if successful
 **/
function pagelines_make_uploads($txt = 'Load'){
add_filter('request_filesystem_credentials', '__return_true' );

	$method = '';
	$url = 'themes.php?page=pagelines';

	if (is_writable(PAGELINES_DCSS)){
		$creds = request_filesystem_credentials($url, $method, false, false, null);
		if ( ! WP_Filesystem($creds) ) {
			// our credentials were no good, ask the user for them again
			request_filesystem_credentials($url, $method, true, false, null);
			return false;
		}

		global $wp_filesystem;
		if ( ! $wp_filesystem->put_contents( PAGELINES_DCSS, $txt, FS_CHMOD_FILE) ) {
			echo 'error saving file!';
			return false;
		}
	}

	return true;
}
/**
 * return array of PageLines plugins.
 * Since 2.0
 */
function pagelines_register_plugins() {

	$pagelines_plugins = array();
	$plugins = get_option('active_plugins');
	if ( $plugins ) {
		foreach( $plugins as $plugin ) {
			$a = get_file_data( WP_PLUGIN_DIR . '/' . $plugin, $default_headers = array( 'pagelines' => 'PageLines' ) );
			if ( !empty( $a['pagelines'] ) ) {
				$pagelines_plugins[] = str_replace( '.php', '', basename($plugin) );
			}

		}
	}
	return $pagelines_plugins;
}

/**
 *
 * Return sorted array based on supplied key
 *
 * @since 2.0
 * @return sorted array
 */
function pagelines_array_sort( $a, $subkey, $pre = null, $dec = null ) {

	if ( ! is_array( $a) || ( is_array( $a ) && count( $a ) <= 1 ) )
		return $a;

	foreach( $a as $k => $v ) {
		$b[$k] = ( $pre ) ? strtolower( $v[$pre][$subkey] ) : strtolower( $v[$subkey] );
	}
	( !$dec ) ? asort( $b ) : arsort($b);
	foreach( $b as $key => $val ) {
		$c[] = $a[$key];
	}
	return $c;
}

/**
 *
 * Polishes a Key for UI presentation
 *
 * @since 2.0
 * @return String
 */
function ui_key($key){

	return ucwords( str_replace( '_', ' ', str_replace( 'pl_', ' ', $key) ) );
}


/**
 *
 * @TODO document
 *
 */
function pl_admin_is_page(){
	global $post;

	if( (isset($_GET['post_type']) && $_GET['post_type'] == 'page')  || (isset($post) && $post->post_type == 'page') )
		return true;
	else
		return false;

}


/**
 *
 * @TODO document
 *
 */
function pl_file_get_contents( $filename ) {

	if ( is_file( $filename ) ) {

		$file = file( $filename, FILE_SKIP_EMPTY_LINES );
		$out = '';
		if( is_array( $file ) )
			foreach( $file as $contents )
				$out .= $contents;

		if( $out )
			return $out;
		else
			return false;
	}
}


/**
 *
 * @TODO document
 *
 */
function pl_detect_ie( $version = false ) {

	global $is_IE;
	if ( ! $version && $is_IE ) {

		return round( substr($_SERVER['HTTP_USER_AGENT'], strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') + 5, 3) );
	}

	if ( $is_IE && is_int( $version ) && stristr( $_SERVER['HTTP_USER_AGENT'], sprintf( 'msie %s', $version ) ) )
		return true;
	else
		return false;
}

/**
 * Search in an array, return full info.
 */
function array_search_ext($arr, $search, $exact = true, $trav_keys = null)
{
  if(!is_array($arr) || !$search || ($trav_keys && !is_array($trav_keys))) return false;
  $res_arr = array();
  foreach($arr as $key => $val)
  {
    $used_keys = $trav_keys ? array_merge($trav_keys, array($key)) : array($key);
    if(($key === $search) || (!$exact && (strpos(strtolower($key), strtolower($search)) !== false))) $res_arr[] = array('type' => "key", 'hit' => $key, 'keys' => $used_keys, 'val' => $val);
    if(is_array($val) && ($children_res = array_search_ext($val, $search, $exact, $used_keys))) $res_arr = array_merge($res_arr, $children_res);
    else if(($val === $search) || (!$exact && (strpos(strtolower($val), strtolower($search)) !== false))) $res_arr[] = array('type' => "val", 'hit' => $val, 'keys' => $used_keys, 'val' => $val);
  }
  return $res_arr ? $res_arr : false;
}

/**
 * PageLines Register Sidebar
 *
 * Registers sidebars with an optional priority.
 *
 * @param   $args
 * @param   null $priorty - numeric value
 *
 * @uses    plotion( 'enable_sidebar_reorder' ) - i.e.: prioritization
 * @uses    pagelines_sidebars class
 *
 * @link    http://www.pagelines.com/wiki/Pagelines_register_sidebar
 */
function pagelines_register_sidebar( $args, $priorty = null ) {

	register_sidebar( $args );
}

/**
 * Insert into array at a position.
 *
 * @param $orig array Original array.
 * @param $new array Array to insert.
 * @param $offset int Offset
 * @return array
 */
function pl_insert_into_array( $orig, $new, $offset ) {

	$newArray = array_slice($orig, 0, $offset, true) +
	            $new +
	            array_slice($orig, $offset, NULL, true);
	return $newArray;
}


/**
 * Insert into array before or after a key.
 *
 * @param $array array Original array.
 * @param $key str Key to find.
 * @param $insert_array array The array data to insert.
 * @param $before bool Insert before or after.
 * @return array
 */
function pl_array_insert( $array, $key, $insert_array, $before = FALSE ) {
	$done = FALSE;
	foreach ($array as $array_key => $array_val) {
		if (!$before) {
			$new_array[$array_key] = $array_val;
		}
		if ($array_key == $key && !$done) {
			foreach ($insert_array as $insert_array_key => $insert_array_val) {
				$new_array[$insert_array_key] = $insert_array_val;
			}
			$done = TRUE;
		}
		if ($before) {
			$new_array[$array_key] = $array_val;
		}
	}
	if (!$done) {
		$new_array = array_merge($array, $insert_array);
	}
	// Put the new array in the place of the original.
	return $new_array;
}

/**
 * Display a banner if suppoerted plugin is detected during template_redirect.
 *
 */
function pl_check_integrations() {

	$integrations = array(

		'bbpress' => array(

			'function'	=> 'is_bbpress',
			'plugin'	=> 'pagelines-bbpress',
			'url'		=> 'http://www.pagelines.com/store/plugins/pagelines-bbpress/',
			'name'		=> 'bbPress',
			'class'		=> 'PageLinesBBPress'

		),
		'jigoshop' => array(

			'function'	=> 'is_jigoshop',
			'plugin'	=> 'pagelines-jigoshop',
			'url'		=> 'http://www.pagelines.com/store/plugins/pagelines-jigoshop/',
			'name'		=> 'Jigoshop',
			'class'		=> 'PageLinesJigoShop'
		),
	);

	foreach( $integrations as $i => $data ) {

		if( function_exists( $data['function'] ) ) {

			if( $data['function']() && ! class_exists( $data['class'] ) )
				pl_check_integrations_banner( $data );
		}
	}
}

function pl_check_integrations_banner( $data ) {

	if( current_user_can('edit_themes') ){

		$banner_title = sprintf( '<h3 class="banner_title wicon">%s was detected.</h3>', $data['name'] );

		$text = 'Looks like your running a supported plugin but your not using our integrations whatsit.';

		$link_text = 'Get it from the store now!';

		$link = sprintf('<a href="%s">%s</a>', $data['url'], $link_text . ' &rarr;');

		echo sprintf('<div class="banner setup_area"><div class="banner_pad">%s <div class="banner_text subhead">%s<br/> %s</div></div></div>', $banner_title, $text, $link);


	}
}

/**
 * Return the raw URI
 *
 * @param $full bool Show full or just request.
 * @return string
 */
function pl_get_uri( $full = true ) {

	if ( $full )
		return  $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	else
		return  $_SERVER["REQUEST_URI"];
}

/**
 * Is framework in debug mode?
 *
 * @return bool
 */
function is_pl_debug() {

	if ( defined( 'PL_DEV' ) && PL_DEV )
		return true;
	if ( pl_setting( 'enable_debug' ) )
		return true;
}

/**
 * Show debug info in footer ( wrapped in tags )
 *
 */
function pl_debug( $text = '', $before = "\n/*", $after = '*/' ) {

	if ( ! is_pl_debug() )
		return;

	$out = sprintf( 'echo "%s %s %s";', $before, $text, $after );
	add_action( 'shutdown', create_function( '', $out ), 9999 );

}

/**
*
* @TODO do
*
*/
function inline_css_markup($id, $css, $echo = true){
	$mark = sprintf('%2$s<style type="text/css" id="%3$s">%2$s %1$s %2$s</style>%2$s', $css, "\n", $id);

	if($echo)
		echo $mark;
	else
		return $mark;
}


function pl_get_image_data( $image_url, $logo = false ) {
			
	if( ! $logo ) {
		$defaults = array( 
			'url'	=>	$image_url,
			'alt'	=> '',
			'title'	=> ''
			);
	} else {
		$defaults = array( 
			'url'	=>	$image_url,
			'alt'	=> esc_attr( get_bloginfo('description') ),
			'title'	=> esc_attr( get_bloginfo('name') )
			);
	}
	
	$ID = _get_image_id_from_url( $image_url );

	if( empty( $ID ) )
		return $defaults;
	
	$data = array();
		
	$data['alt'] = get_post_meta( $ID, '_wp_attachment_image_alt', true );
	if( '' === $data['alt'] )
		unset( $data['alt'] );

	$data['title'] = get_the_title( $ID );
	if( false !== ( strpos( $data['title'], 'PageLines-' ) ) || '' === $data['title'] )
		unset( $data['title'] );

	return wp_parse_args( $data, $defaults );
}

function _get_image_id_from_url($image_url) {

	global $wpdb;
	$prefix = $wpdb->prefix;
	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM " . $prefix . "posts" . " WHERE guid='%s';", $image_url ));

    return ( is_array( $attachment ) && isset( $attachment[0])) ? $attachment[0] : array(); 
}