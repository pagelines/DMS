<?php

class EditorLess extends EditorLessHandler {

	var $pless;

	function __construct( PageLinesLess $pless ) {

		$this->pless = $pless;
		$this->draft_less_file = sprintf( '%s/editor-draft.css', PageLinesRenderCSS::get_css_dir( 'path' ) );

		if( $this->is_draft() )
			$this->draft_init();
	}

	/**
	 * Create a JS variable for using LESS data on the front end
	 */
	function localize_less_data() {
		$data = array(
			'vars'   => $this->prepare_vars(),
			'mixins' => $this->get_less_file_uri('mixins'),
			'colors' => $this->get_less_file_uri('colors'),
		);

		wp_localize_script( 'pl-less-parser', 'pl_less_data', $data );
	}

	/**
	 * Format vars to be used with less.modifyVars()
	 * @return array
	 */
	function prepare_vars() {
		$vars = array();
		foreach ( $this->pless->constants as $var => $value )
			$vars["@$var"] = trim( $value, '"' ); // trim prevents values from being double quoted by json_encode
	
		return $vars;
	}

	/**
	 * Get the URI for the right less file to be used
	 */
	function get_less_file_uri( $file ) {
		$path = $this->pless->get_less_filepath( $file );
		$url = str_replace(WP_CONTENT_DIR, WP_CONTENT_URL, $path);
		return $url; 
	}

	/**
	 * Output raw core less into footer for use with less.js
	 * Will output the same LESS that is used when compiling with PHP
	 * Allows for all custom variables, mixins, as well as any filtered/overriden files
	 */
	function print_core_less() {
		
		$core_less = $this->pless->add_constants() . $this->pless->add_bootstrap();

		printf('<div id="pl_core_less" style="display:none;">%s</div>', 
			$this->minify( $core_less )
		);
	}

	/**
	 *
	 *  Display Draft Less.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 */
	function pagelines_draft_render() {

		if( isset( $_GET['pagedraft'] ) ) {

			$this->compare_less();

			header( 'Content-type: text/css' );
			header( 'Expires: ' );
			header( 'Cache-Control: max-age=604100, public' );

			if( is_file( $this->draft_less_file ) ) {
				echo readfile( $this->draft_less_file );
			} else {
				$core = $this->googlefont_replace( $this->get_draft_core() );
				$css = $this->minify( $core['compiled_core'] );
				$css .= $this->minify( $core['compiled_sections'] );
				
				$css .= $this->minify( $core['dynamic'] );
				$this->write_draft_less_file( $css );
				echo $css;
			}
			die();
		}
	}

} // EditorLess