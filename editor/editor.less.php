<?php

class EditorLess extends EditorLessHandler {

	var $pless;
	var $lessfiles;

	function __construct( PageLinesLess $pless ) {

		$this->pless = $pless;
		$this->lessfiles = $this->get_core_lessfiles();
		$this->draft_less_file = sprintf( '%s/editor-draft.css', PageLinesRenderCSS::get_css_dir( 'path' ) );

		if( $this->is_draft() )
			$this->draft_init();
	}

	/**
	 * Output raw core less into footer for use with less.js
	 * Will output the same LESS that is used when compiling with PHP
	 * Allows for all custom variables, mixins, as well as any filtered/overriden files
	 */
	function print_core_less() {
		
		$core_less = $this->pless->add_constants('') . $this->pless->add_bootstrap();

		printf('<div id="pl_core_less" style="display:none;">%s</div>', 
			$this->minify( $core_less )
		);
	}

	/**
	 *
	 *  Display Draft Less.
	 *
	 *  @package PageLines DMS
	 *  @since 3.0
	 */
	function pagelines_draft_render() {

		if( isset( $_GET['pagedraft'] ) ) {

			global $post;
			$this->compare_less();

			header( 'Content-type: text/css' );
			header( 'Expires: ' );
			header( 'Cache-Control: max-age=604100, public' );

			// If you set a static home page in WordPress then delete it you get no CSS, this fixes it ( WhitchCraft! )
			if( ! is_object( $post ) )
				header( 'Stefans Got No Pages', true, 200 );

			if( is_file( $this->draft_less_file ) ) {
				echo pl_file_get_contents( $this->draft_less_file );
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