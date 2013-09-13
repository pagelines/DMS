<?php
/**
 *
 *  PageLines Less Language Parser
 *
 *  @package PageLines DMS
 *	@subpackage Less
 *  @since 2.0.b22
 *
 */
class PageLinesLess {

	private $lparser = null;
	public $constants = '';

	/**
     * Establish the default LESS constants and provides a filter to over-write them
     *
     * @uses    pl_hashify - adds # symbol to CSS color hex values
     * @uses    page_line_height - calculates a line height relevant to font-size and content width
     */
	function __construct() {

		global $less_vars;

		// PageLines Variables
		$constants = array(
			'plRoot'				=> sprintf( "\"%s\"", PL_PARENT_URL ),
			'plCrossRoot'			=> sprintf( "\"//%s\"", str_replace( array( 'http://','https://' ), '', PL_PARENT_URL ) ),
			'plSectionsRoot'		=> sprintf( "\"%s\"", PL_SECTION_ROOT ),
			'plPluginsRoot'			=> sprintf( "\"%s\"", WP_PLUGIN_URL ),
			'plChildRoot'			=> sprintf( "\"%s\"", PL_CHILD_URL ),
			'plExtendRoot'			=> sprintf( "\"%s\"", PL_EXTEND_URL ),
			'plPluginsRoot'			=> sprintf( "\"%s\"", plugins_url() ),
		);

		if(is_array($less_vars))
			$constants = array_merge($less_vars, $constants);


		$this->constants = apply_filters('pless_vars', $constants);
	}


	public function raw_less( $lesscode, $type = 'core' ) {

		return $this->raw_parse($lesscode, $type);
	}

	private function raw_parse( $pless, $type ) {

		require_once( PL_INCLUDES . '/less.plugin.php' );

		if( ! $this->lparser )
			$this->lparser = new plessc();

		$pless = $this->add_constants( '' ) . $this->add_bootstrap() . $pless;

		try {
			$css = $this->lparser->compile( $pless );
		} catch ( Exception $e) {
			plupop( "pl_less_error_{$type}", $e->getMessage() );
			return sprintf( "/* LESS PARSE ERROR in your %s CSS: %s */\r\n", ucfirst( $type ), $e->getMessage() );
		}

		// were good!
		plupop( "pl_less_error_{$type}", false );
		return $css;
	}

	function add_bootstrap( ) {
		$less = '';

		$less .= $this->load_less_file( 'variables' );
		$less .= $this->load_less_file( 'colors' );
		$less .= $this->load_less_file( 'mixins' );

		return $less;
	}

	public static function load_less_file( $file ) {

		$file 	= sprintf( '%s.less', $file );
		$parent = sprintf( '%s/%s', PL_CORE_LESS, $file );
		$child 	= sprintf( '%s/%s', PL_CHILD_LESS, $file );

		// check for child 1st if not load the main file.

		if ( is_file( $child ) )
			return pl_file_get_contents( $child );
		else
			return pl_file_get_contents( $parent );
	}


	private function add_core_less($pless){

		global $disabled_settings;

		$add_color = (isset($disabled_settings['color_control'])) ? false : true;
		$color = ($add_color) ? pl_get_core_less() : '';
		return $pless . $color;
	}

	function add_constants( $pless ) {

		$prepend = '';

		foreach($this->constants as $key => $value)
			$prepend .= sprintf('@%s:%s;%s', $key, $value, "\n");

		return $prepend . $pless;
	}

}

/*
 * Add Less Variables
 *
 * Must be added before header.
 **************************/
function pagelines_less_var( $name, $value ){

	global $less_vars;

	$less_vars[$name] = $value;

}


/*
 *  Color Fetch
 **************************/
function pl_base_color( $mode = '', $difference = '10%'){

	$base_color = PageLinesThemeSupport::BaseColor();

	if( !$base_color ){

		if( pl_check_color_hash( ploption('contentbg' ) ) )
			$base = pl_hash_strip( ploption('contentbg') );
		elseif( pl_check_color_hash( ploption('pagebg' ) ) )
			$base = pl_hash_strip( ploption('pagebg') );
		elseif( pl_check_color_hash( ploption('bodybg' ) ) )
			$base = pl_hash_strip( ploption('bodybg') );
		else
			$base = 'FFFFFF';

	} else
		$base = $base_color;


	if($mode != ''){

		$adjust_base = new PageLinesColor($base);

		$adjusted = $adjust_base->c($mode, $difference);

		return $adjusted;

	} else
		return $base;
}


/**
 * PageLines BackGround Color
 *
 * Use to set the background color, if set; if not set the background color is returned as #FFFFFF (White)
 *
 * @return bool|string - background color value
 */
function pl_bg_color(){

	if( pl_check_color_hash( get_set_color( 'the_bg' ) ) )
		return get_set_color( 'the_bg' );
	else
		return 'FFFFFF';
}

/**
 * PageLines Text Color
 *
 * Used to set the text color; if not set the default color #000000 is set
 *
 * @return mixed|string - text color value
 */
function pl_text_color(){

	$color = ( pl_check_color_hash( ploption( 'text_primary' ) ) ) ? pl_hash_strip( ploption( 'text_primary' ) ) : '000000';

	return $color;
}

/**
 * PageLines Link Color
 *
 * Used to set the link; if not set the default color is set to #225E9B
 *
 * @return mixed|string - link color
 */
function pl_link_color(){

	$color = ( pl_check_color_hash( ploption( 'linkcolor' ) ) ) ? pl_hash_strip( ploption( 'linkcolor' ) ) : '225E9B';

	return $color;
}

/**
 * PageLines Header Color
 *
 * Used to set the header color; if not set the default color #000000 is set
 *
 * @return mixed|string - header color
 */
function pl_header_color(){

	$color = ( pl_check_color_hash( ploption( 'headercolor' ) ) ) ? pl_hash_strip( ploption( 'headercolor' ) ) : '000000';

	return $color;
}

/**
 * PageLines Footer Color
 *
 * Used to set the footer text color; if not set the default color #999999 is set
 *
 * @return mixed|string
 */
function pl_footer_color(){

	$color = ( pl_check_color_hash( ploption( 'footer_text' ) ) ) ? pl_hash_strip( ploption( 'footer_text' ) ) : '999999';

	return $color;
}

/*
 *  Helpers
 **************************/
function pl_hash_strip( $color ){

	return str_replace('#', '', $color);
}

function pl_check_color_hash( $color ) {

	if ( preg_match( '/^#?([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color ) )
		return true;
	else
		return false;
}

/**
 * PageLines Hashify
 *
 * Adds the # symbol to the hex value of the color being used
 *
 * @param $color
 *
 * @return string
 */
function pl_hashify( $color ){

	if( is_int( $color ) )
		$color = strval( $color );

	$clean_hex = str_replace('#', '', $color);

	return sprintf('#%s', $clean_hex);
}