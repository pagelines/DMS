<?php 


class PLCSSEditor {
	
	
	function __construct(){
		
		
		
		wp_enqueue_script( 'pl-less-parser',	PL_JS . '/utils.less.js', array( 'jquery' ), PL_CORE_VERSION, true );
		
		add_action( 'wp_head', array( $this, 'load_less') );
		
		
	}
	
	function less_files(){
		
	}
	
	function load_less(){
		
		?>
		<style type="text/css" id="pagelines-less-css">
		.hello{}
		</style>
		<?php 
		
	}
	
}
