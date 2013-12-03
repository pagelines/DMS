<?php

$plinstall = new PageLinesInstall;

class PageLinesInstall{
	
	function __construct(){
	
		$this->activate_url = apply_filters('pl_activate_url', home_url().'?plnew=core');
		
		$this->getting_started_pagename = 'PageLines Getting Started';
		
		add_action( 'pagelines_admin_load', array($this, 'pagelines_check_install') );
	}
	
	function pagelines_check_install() {

		global $pagenow;

		if( ($pagenow == 'customize.php')
			|| ( isset($_GET['activated'] ) && $pagenow == "themes.php" )
		){
			$id = $this->add_getting_started();
			
			$url = add_query_arg( 'plinstall', 'core', get_permalink( $id ) );
			
			wp_redirect( $url ); exit;
		}
			
	}
	
	function add_getting_started(){
		
		// Check or add page (leave in draft mode)
		$pages = get_pages();
		$page_exists = false;
		foreach ($pages as $page) { 
			
			$name = $page->post_name;
			
			if ( $name == $this->getting_started_pagename ) { 
				$page_exists = true;
				$id = $page->ID;
			}
			 
		}
		
		if( ! $page_exists ){
			
			global $user_ID;

			$page = array(
				'post_type'		=> 'page',
				'post_title'	=> $this->getting_started_pagename,
				'post_status'	=> 'draft',
				'post_author'	=> $user_ID,
				'post_content'	=> $this->getting_started_content()

			);

			$id = wp_insert_post (  apply_filters('pl_getting_started_page', $page) );
			
		}
		return $id;
	}
	
	function getting_started_content(){
		
		ob_start(); 
		
		?>
		<h3>Welcome to PageLines DMS!</h3>
		<p>A cutting-edge drag & drop design management system for your website. <br/>Watch the video below for help getting started.</p>
		<iframe width='700' height='420' src='//www.youtube.com/embed/BracDuhEHls?rel=0&vq=hd720' frameborder='0' allowfullscreen></iframe>
		
		<?php 
		
		return ob_get_clean();
		
	}
	
	
}
