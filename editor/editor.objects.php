<?php 


/* 
 * User object abstraction class.
 */ 
class PLCustomObjects{
	
	function __construct( $slug ){
		
		$this->slug = $slug;
		
		$this->objects = $this->get_all();
	}
	
	function default_objects(){
		$d = array();
		return $d;
	}
	
	function default_fields(){
		$d = array(
			'name'	=> __( 'No Name', 'pagelines' ),
			'desc'	=> '', 
			'map'	=> array(),
			'settings'	=> array()
		);
		
		return $d;
	}
	
	function get_all(){
		
		 return pl_opt( $this->slug, $this->default_objects() );
	
	}
	
	function update_all(){
		pl_opt_update( $this->slug, $this->objects );
	}
	
	function create( $args = array() ){
		
		$args = wp_parse_args( $args, $this->default_fields());
		
		$key = pl_create_id( $args['name'] );

		$new = array( $key => $args );

		$this->objects = array_merge( $new, $this->objects );
		
		$this->update_all();
		
		return $key;
	}
	
	function retrieve( $key ){
		
		if( isset( $this->objects[ $key ]) )
			return wp_parse_args( $this->objects[ $key ], $this->default_fields() ); 
		else
			return false;
			
	}
	
	function retrieve_field( $key, $field ){
		$object = $this->retrieve( $key ); 
		
		if( $object && isset( $object[$field ]) )
			return $object[$field ]; 
		else
			return false;
	}
	
	function update( $key, $args ){
		
		$object = ( isset($this->objects[ $key ]) ) ? $this->objects[ $key ] : array();
		
		$this->objects[ $key ] = wp_parse_args( $args, $object );
		
		$this->update_all();
		
		return $key;
		
	}
	
	function delete( $key ){
		
		if( isset( $this->objects[ $key ] ) ){
			
			unset( $this->objects[ $key ] );
			$this->update_all();
			return $key;
			
		} else
			return false;
			
	}
	
}
