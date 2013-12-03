<?php

/* 
 * Enables and disables funcationality primarily of interest to advanced and developer users. 
 */ 
class PLDeveloperTools {
	
	
	function __construct(){

		if( ! is_pl_debug() )
			return;

		// Add tab to toolbar 
		add_filter('pl_toolbar_config', array( $this, 'toolbar'));
		
		// Add developer settings to JSON blob
		add_filter('pl_json_blob_objects', array( $this, 'add_to_blob'));
		
		add_action('wp_footer', array( $this, 'draw_developer_data'), 200);

		$this->url = PL_PARENT_URL . '/editor';
		
		global $pl_perform; 
		$pl_perform = array(); 
	}
	

	function draw_developer_data(){
	
			?><script>
				!function ($) {

					$.plDevData = {
						<?php echo $this->pl_performance_object();?>
					}


				}(window.jQuery);
			</script>
			<?php

	}
	
	function pl_performance_object(){
		
		// blob objects to add to json blob // format: array( 'name' => array() )
		$blob_objects = apply_filters('pl_performance_object', $this->basic_performance() ); 
		
		$output = '';
		if( ! empty($blob_objects) ){
			
			foreach( $blob_objects as $name => $array ){
				$output .= sprintf('%s:%s, %s', $name, json_encode( pl_arrays_to_objects( $array ) ), "\n\n");
			}
		}
		
		return $output;
		
	}
	
	function basic_performance(){
		
		global $pl_start_time, $pl_start_mem, $pl_perform;
		
		
		
		$pl_perform['memory'] = array(
			'num'		=> round( (memory_get_usage() - $pl_start_mem) / (1024 * 1024), 3 ),
			'label'		=> 'MB',
			'title'		=> 'Editor Memory',
			'info'		=> 'Amount of memory used by the DMS editor in MB during this page load.'
		);
		
		$pl_perform['queries'] = array(
			'num'		=> get_num_queries(),
			'label'		=> 'Queries',
			'title'		=> 'Total Queries',
			'info'		=> 'The number of database queries during the WordPress/Editor execution.'
		);
		
		$pl_perform['total_time'] = array(
			'num'		=> timer_stop( 0 ),
			'label'		=> 'Seconds',
			'title'		=> 'Total Time',
			'info'		=> 'Total time to render this page including WordPress and DMS editor.'
		);
		
		$pl_perform['time'] = array(
			'num'		=> round( microtime(TRUE) - $pl_start_time, 3),
			'label'		=> 'Seconds',
			'title'		=> 'Editor Time',
			'info'		=> 'Amount of time it took to load this page once DMS had started.'
		);
		
		return $pl_perform;
		
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
					'key'		=> 'fill-in',
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
					'key'		=> 'fill-in',
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

function pl_add_perform_data( $data_point, $title, $label, $description){
	global $pl_perform;
	
	$pl_perform[] = array(
		'title'		=> $title, 
		'num'		=> $data_point,
		'label'		=> $label,
		'info'		=> $description
	);
}
