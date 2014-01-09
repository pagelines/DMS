<?php


class PLLove {
	
	 function __construct()   {	
		
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );
        add_action( 'wp_ajax_pl_love', array( $this, 'ajax' ) );
		add_action( 'wp_ajax_nopriv_pl_love', array( $this, 'ajax' ) );
		
	}
	
	function enqueue_scripts() {
		
		wp_localize_script( 'pagelines-common', 'plLove', array( 'ajaxurl' => admin_url('admin-ajax.php') ));
		
	}
	
	function ajax($post_id) {
		
		if( isset($_POST['loves_id']) ) {
			
			$post_id = str_replace('pl-love-', '', $_POST['loves_id']);
			echo $this->love_post($post_id, 'update');
		
		} else {
			$post_id = str_replace('pl-love-', '', $_POST['loves_id']);
			echo $this->love_post($post_id, 'get');
		}
		
		exit;
	}
	
	
	function love_post($post_id, $action = 'get') {
	
		if( ! is_numeric($post_id) ) 
			return;
	
		switch($action) {
		
			case 'get':
				$love_count = get_post_meta($post_id, '_pl_love', true);
				if( !$love_count ){
					$love_count = 0;
					add_post_meta($post_id, '_pl_love', $love_count, true);
				}
				
				return '<span class="pl-love-count">'. $love_count .'</span>';
				break;
				
			case 'update':
				$love_count = get_post_meta($post_id, '_pl_love', true);
				if( isset($_COOKIE['pl_love_'. $post_id]) ) return $love_count;
				
				$love_count++;
				update_post_meta($post_id, '_pl_love', $love_count);
				setcookie('pl_love_'. $post_id, $post_id, time()*20, '/');
				
				return sprintf( '<span class="pl-love-count">%s</span>', $love_count );
				break;
		
		}
	}


	function add_love() {
		global $post;

		$output = $this->love_post($post->ID);
  
  		$class = 'pl-love';
  		$title = __('Love this', 'pagelines');
		if( isset( $_COOKIE['pl_love_'. $post->ID] ) ){
			$class = 'pl-love loved';
			$title = __('You already love this!', 'pagelines');
		}
		
		return sprintf('<a href="#" class="%s" id="pl-love-%s" title="%s"> <i class="icon-heart pl-love-heart"></i> %s</a>', 
						$class, 
						$post->ID,
						$title,
						$output
					);
		
	}
	
}


global $pl_love;
$pl_love = new PLLove();

function pl_love( ) {
	
	global $pl_love;

	return $pl_love->add_love(); 
	
}

