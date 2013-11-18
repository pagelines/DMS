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
		
		add_action('wp_footer', array( $this, 'draw_developer_data'), 200);

		$this->url = PL_PARENT_URL . '/editor';
	}

	function draw_developer_data(){
		global $pl_start_time, $pl_start_mem;

			?><script>
				!function ($) {

					$.plDevData = {
						php: {
							memory: {
								num: '<?php echo round( (memory_get_usage() - $pl_start_mem) / (1024 * 1024), 3 );?>'
								, label: 'MB'
								, title: 'Editor Memory'
								, info: 'Amount of memory used by the DMS editor in MB during this page load.'
							}
							
							, queries: {
								num: '<?php echo get_num_queries(); ?>'
								, label: 'Queries'
								, title: 'Total WP Queries'
								, info: 'Retrieve the number of database queries during the WordPress/Editor execution.'
							}
							, total_time: {
								num: '<?php echo timer_stop( 0 ); ?>'
								, label: 'Seconds'
								, title: 'Total Page Render Time'
								, info: 'Total time to render this page including WordPress and DMS editor.'
							}
							, time: {
								num: '<?php echo round( microtime(TRUE) - $pl_start_time, 3); ?>'
								, label: 'Seconds'
								, title: 'Editor Time'
								, info: 'Amount of time it took to load this page once DMS had started.'
							}
							
						}

					}


				}(window.jQuery);
			</script>
			<?php

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
		
		
		
		$settings['dev_log'] = array(
			'name' 	=> __( 'Logging', 'pagelines' ),
			'icon'	=> 'icon-wrench',
			'opts' 	=> array(

				array(
					'type' 		=> 	'template',
					'template'	=> 'Nothing appears to have been logged.'
				),
			),
			'class'	=> 'dev_logging'
		);
		
		$settings['dev-page'] = array(
			'name' 	=> __( 'Performance', 'pagelines' ),
			'icon'	=> 'icon-wrench',
			'opts' 	=> array(
				array(
					'type' 		=> 	'template',
					'template'	=> 'No performance data exists on the page.'
				),
			),
		);
		
		$settings['devopts'] = array(
			'name' 	=> __( 'Options', 'pagelines' ),
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
					'key'		=> 'less_dev_mode',
					'col'		=> 1, 
					'type' 		=> 'check',
					'label' 	=> __( 'Enable LESS dev mode', 'pagelines' ),
					'title' 	=> __( 'LESS Developer Mode', 'pagelines' ),
					'help' 		=> __( 'Enables LESS recompile on every editor load, useful when doing a lot of graphical LESS development since you dont have to manually hit publish to recompile.', 'pagelines' )
				),
				array(
					'key'		=> 'no_cache_mode',
					'col'		=> 2, 
					'type' 		=> 'check',
					'label' 	=> __( 'Enable no cache mode', 'pagelines' ),
					'title' 	=> __( 'No Cache Mode', 'pagelines' ),
					'help' 		=> __( '@simon explanation needed', 'pagelines' )
				),
			);
			
		return $settings;

	}
	
	
}
