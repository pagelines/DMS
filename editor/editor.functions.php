<?php

/*
 *	Editor functions - Always loaded
 */

function pl_has_editor(){

	return (class_exists('PageLinesTemplateHandler')) ? true : false;

}


// Function to be used w/ compabibility mode to de
function pl_deprecate_v2(){

	if(pl_setting('enable_v2'))
		return false;
	else 
		return true;

}


function pl_use_editor(){

	return true;

}


// Process old function type to new format
function process_to_new_option_format( $old_options ){

	$new_options = array();

	foreach($old_options as $key => $o){

		if($o['type'] == 'multi_option' || $o['type'] == 'text_multi'){

			$sub_options = array();
			foreach($o['selectvalues'] as $sub_key => $sub_o){
				$sub_options[ ] = process_old_opt($sub_key, $sub_o, $o);
			}
			$new_options[ ] = array(
				'type' 	=> 'multi',
				'title'	=> $o['title'],
				'opts'	=> $sub_options
			);
		} else {
			$new_options[ ] = process_old_opt($key, $o);
		}

	}

	return $new_options;
}

function process_old_opt( $key, $old, $otop = array()){

	if(isset($otop['type']) && $otop['type'] == 'text_multi')
		$old['type'] = 'text';

	$defaults = array(
        'type' 			=> 'check',
		'title'			=> '',
		'inputlabel'	=> '',
		'exp'			=> '',
		'shortexp'		=> '',
		'count_start'	=> 0,
		'count_number'	=> '',
		'selectvalues'	=> array(),
		'taxonomy_id'	=> '',
		'post_type'		=> '',
		'span'			=> 1,
		'default'		=> 1
	);

	$old = wp_parse_args($old, $defaults);

	$exp = ($old['exp'] == '' && $old['shortexp'] != '') ? $old['shortexp'] : $old['exp'];

	if($old['type'] == 'text_small'){
		$type = 'text';
	} elseif($old['type'] == 'colorpicker'){
		$type = 'color';
	} elseif($old['type'] == 'check_multi'){
		$type = 'multi';
		
		foreach($old['selectvalues'] as $key => &$info){
			$info['type'] = 'check';
		}
	} else
		$type = $old['type'];

	$new = array(
		'key'			=> $key,
		'title'			=> $old['title'],
		'label'			=> $old['inputlabel'],
		'type'			=> $type,
		'help'			=> $exp,
		'opts'			=> $old['selectvalues'],
		'span'			=> $old['span'],
	);

	if($old['type'] == 'count_select'){
		$new['count_start'] = $old['count_start'];
		$new['count_number'] = $old['count_number'];
	}

	if($old['taxonomy_id'] != '')
		$new['taxonomy_id'] = $old['taxonomy_id'];

	if($old['post_type'] != '')
		$new['post_type'] = $old['post_type'];
		
	if($old['default'] != '')
		$new['default'] = $old['default'];

	return $new;
}

function pl_create_id( $string ){

	$string = str_replace( ' ', '_', trim( strtolower( $string ) ) );
	$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);

	return $string;
}

function pl_new_clone_id(){
	return substr(uniqid(), -6);
}


function pl_create_int_from_string( $str ){
	
	return (int) substr( preg_replace("/[^0-9,.]/", "", md5( $str )), -6);
}


/*
 * Lets document utility functions
 */
function pl_add_query_arg( $args ) {

	global $wp;
	$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
	return add_query_arg( $args, $current_url );
}

/*
 * This function recursively converts an multi dimensional array into a multi layer object
 * Needed for json conversion in < php 5.2
 */
function pl_arrays_to_objects( array $array ) {

	$objects = new stdClass;

	if( is_array($array) ){
		foreach ( $array as $key => $val ) {

			if($key === ''){
				$key = 0;
			}

	        if ( is_array( $val ) && !empty( $val )) {


				$objects->{$key} = pl_arrays_to_objects( $val );

	        } else {

	            $objects->{$key} = $val;

	        }
	    }

	}

    return $objects;
}

function pl_animation_array(){
	$animations = array(
		'no-anim'			=> 'No Animation',
		'pla-fade'			=> 'Fade',
		'pla-scale'			=> 'Scale',
		'pla-from-left'		=> 'From Left',
		'pla-from-right'	=> 'From Right', 
		'pla-from-bottom'	=> 'From Bottom', 
		'pla-from-top'		=> 'From Top', 
	); 
	
	return $animations;
}

/**
 * Get an array of all Font Awesome .icon-`SLUG`s
 * Dynamically populated from /less/icons.less
 */
function pl_icon_array() {

	// have we done this before?
	// if so, check if it's still good
	if ( is_array( $saved = get_option('pl_icons') ) ) {
		$d = array(
			'version' => '',
			'icons'   => ''
		);
		$saved = wp_parse_args( $saved, $d );
		
		if ( $saved['version'] == PL_CORE_VERSION && is_array( $saved['icons'] ) )
			return $saved['icons'];
	}

	// build/regenerate the new array from icons.less

	$iconfile   = pl_file_get_contents( PL_CORE_LESS . '/icons.less' );
	$start      = strpos( $iconfile, '.icon-glass:before'); // find the pos of the first icon
	$icons_str  = substr( $iconfile, $start ); // slice off the part we want
	// get them all
	preg_match_all('/\.icon-([^:]+)/', $icons_str, $icons);

	// grab all the captured matches
	$icon_array = $icons[1];
	sort( $icon_array );

	// store away for a while
	update_option('pl_icons', array(
		'version' => PL_CORE_VERSION,
		'icons'   => $icon_array
	) );

	return $icon_array;
}

function pl_button_classes(){
	$array = array(
		''			 		=> 'Default',
		'btn-primary'		=> 'Dark Blue',
		'btn-info'			=> 'Light Blue',
		'btn-success'		=> 'Green',
		'btn-warning'		=> 'Orange',
		'btn-danger'		=> 'Red',
		'btn-inverse'		=> 'Black',
	); 
	return $array;
}

function get_sidebar_select(){


	global $wp_registered_sidebars;
	$allsidebars = $wp_registered_sidebars;
	ksort($allsidebars);

	$sidebar_select = array();
	foreach($allsidebars as $key => $sb){

		$sidebar_select[ $sb['id'] ] = array( 'name' => $sb['name'] );
	}

	return $sidebar_select;
}

function pl_count_sidebar_widgets( $sidebar_id ){

	$total_widgets = wp_get_sidebars_widgets();

	if(isset($total_widgets[ $sidebar_id ]))
		return count( $total_widgets[ $sidebar_id ] );
	else
		return false;
}

function pl_enqueue_script(  $handle, $src = false, $deps = array(), $ver = false, $in_footer = false ){
	
	global $wp_scripts;
	
	wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
}

function pl_add_theme_tab( $array ){
	
	global $pl_user_theme_tabs;
	
	if(!isset($pl_user_theme_tabs) || !is_array($pl_user_theme_tabs))
		$pl_user_theme_tabs = array(); 
		
		
	$pl_user_theme_tabs = array_merge($array, $pl_user_theme_tabs); 
	
	
	
}



