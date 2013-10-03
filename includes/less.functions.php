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


