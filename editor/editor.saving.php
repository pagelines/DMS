<?php



class PageLinesSave {
	
	
	function __construct(){
		
		
		add_filter( 'pl_ajax_fast_save', array( &$this, 'fast_save' ), 10, 2 );
		
	}
	
	
	function fast_save( $response, $data ){
		
		if( $data['run'] == 'map' ){
			$response = $this->save_map( $response, $data );
		}
		

		
		return $response;
		
	}
	
	/* 
	 * Saves only Map Data based on template mode (local or type)
	 */ 
	function save_map( $response, $data ){
	
		$global_map = array(
			'header' => $data['store']['header'],
			'footer' => $data['store']['footer'],
		);
		
		$local_map = array(
			'template' => $data['store']['template']
		);
		
		$template_mode = $data['templateMode']; 
		
		$metaID = ( $template_mode == 'type' ) ? $data['typeID'] : $data['pageID'];
		
		
		$global_settings = pl_settings();
		$global_settings['regions'] = $global_map;
		pl_settings_update( $global_settings );
		
		$local_settings = pl_settings( 'draft', $metaID );
		$local_settings['custom-map'] = $local_map;
		pl_settings_update( $local_settings, 'draft', $metaID );
		
	
		return $response;
	}
}