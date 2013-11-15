<?php

/* 
 * Enables and disables funcationality primarily of interest to advanced and developer users. 
 */ 
class PLDeveloperTools {
	
	
	function __construct(){

		if( ! PL_DEV )
			return;

		// Add tab to toolbar 
		add_filter('pl_toolbar_config', array( $this, 'toolbar'));
		
		// Add developer settings to JSON blob
		add_filter('pl_json_blob_objects', array( $this, 'add_to_blob'));
		
		add_action('pagelines_editor_scripts', array( $this, 'scripts'));

		$this->url = PL_PARENT_URL . '/editor';
	}

	function scripts(){


	}
	
	function add_to_blob( $objects ){
		
		$objects['dev'] = $this->get_set();
		return $objects;
		
	}

	function toolbar( $toolbar ){

		$toolbar[ 'dev' ] = array(
			'name'	=> __( 'Developer', 'pagelines' ),
			'icon'	=> 'icon-wrench',
			'pos'	=> 105,
			'panel'	=> $this->get_settings_tabs()
		
		);


		return $toolbar;
	}
	
	function get_settings_tabs(){

		$tabs = array();

		$tabs['heading'] = __( 'Developer Tools', 'pagelines' );

		foreach( $this->get_set() as $tabkey => $tab ){

			$tabs[ $tabkey ] = array(
				'key' 	=> $tabkey,
				'name' 	=> $tab['name'],
				'icon'	=> isset($tab['icon']) ? $tab['icon'] : ''
			);
		}
	
		return $tabs;

	}
	

	function get_set( ){

		$settings = array(); 
		
		$settings['devopts'] = array(
			'name' 	=> __( 'Dev Options', 'pagelines' ),
			'icon'	=> 'icon-wrench',
			'opts' 	=> $this->basic()
		);

		$settings = apply_filters( 'pl_developer_settings_array', $settings );

		$default = array(
			'icon'	=> 'icon-edit',
			'pos'	=> 100
		);

		foreach($settings as $key => &$info){
			$info = wp_parse_args( $info, $default );
		}
		unset($info);

		uasort($settings, "cmp_by_position" );

		return apply_filters('pl_sorted_developer_array', $settings);
	}


	function basic(){

		$settings = array(

			array(
				'key'			=> 'pagelines_favicon',
				'label'			=> __( 'Upload Favicon (32px by 32px)', 'pagelines' ),
				'type' 			=> 	'image_upload',
				'imgsize' 			=> 	'16',
				'extension'		=> 'ico,png', // ico support
				'title' 		=> 	__( 'Favicon Image', 'pagelines' ),
				'help' 			=> 	__( 'Enter the full URL location of your custom <strong>favicon</strong> which is visible in browser favorites and tabs.<br/> <strong>Must be .png or .ico file - 32px by 32px</strong>.', 'pagelines' ),
				'default'		=>  '[pl_parent_url]/images/default-favicon.png'
			),


		);

		return $settings;

	}
	
	
}
