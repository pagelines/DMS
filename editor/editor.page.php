<?php
/**
 * 
 *
 *  PageLines Page Handling
 *
 *
 */
class PageLinesPage {

	var $special_base = 70000000;
	var $opt_special_lookup = 'pl-special-lookup';

	function __construct( ) {
		
		$this->type = $this->type();
		
		$this->type_name = ucwords( str_replace('_', ' ', $this->type()) ); 
		
		$this->id = $this->id();

	}

	function id(){
		global $post;
		
		if(!$this->is_special() && isset($post) && is_object($post))
			return $post->ID;
		else
			return $this->special_id();
			
	}
	
	function special_id(){
		
		$index = $this->special_index_lookup();
		
		$id = $this->special_base + $index; 
		
		return $id;
		
	}

	function special_index_lookup(){
		
		$lookup_array = get_option( $this->opt_special_lookup );
		
		if( !$lookup_array ){
			
			$lookup_array = array(
				'blog',
				'category',
				'search', 
				'tag',
				'author',
				'archive',
				'page',
				'post',
				'404_page'
			);
			
			update_option( $this->opt_special_lookup, $lookup_array );
		}
		
		$index = array_search( $this->type(), $lookup_array );
		
		if( !$index ){
			
			$lookup_array[]  = $this->type();
			
			$index = array_search( $this->type(), $lookup_array );
			
			update_option( $this->opt_special_lookup, $lookup_array );
			
		}
		
		return $index;
		
	}

	function type(){

		if( is_404() )
			$type = '404_page';
			
		elseif( pl_is_cpt('archive') )
			$type = get_post_type_plural();
			
		elseif( is_tag() )
			$type = 'tag';
			
		elseif( is_search() )
			$type = 'search';
			
		elseif( is_category() )
			$type = 'category';
			
		elseif( is_author() )
			$type = 'author';
			
		elseif( is_archive() )
			$type = 'archive';
			
		elseif( is_home() )
			$type = 'blog';
	
		// ID is now set... 
		elseif( pl_is_cpt() )
			$type = get_post_type();
			
		elseif( is_page() )
			$type = 'page';
			
		elseif( is_single() )
			$type = 'post';
			
		else
			$type = 'other';
			
		return $type;

	}
	
	function is_special(){
		
		if ( is_404() || is_home() || is_search() || is_archive() ) 
			return true;
		else 
			return false;
		
	}
	

}

