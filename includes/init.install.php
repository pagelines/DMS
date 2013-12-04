<?php

$plinstall = new PageLinesInstall;

class PageLinesInstall{
	
	function __construct(){
	
		$this->activate_url = apply_filters('pl_activate_url', home_url().'?plnew=core');
		
		$this->getting_started_pagename = 'PageLines Getting Started';
		
		$this->getting_started_pageslug = 'pl-getting-started';
	
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
		$pages = get_pages( array( 'post_status' => 'draft' ) );
		$page_exists = false;
		foreach ($pages as $page) { 
			
			$name = $page->post_name;
			
	
			if ( $name == $this->getting_started_pageslug ) { 
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
				'post_content'	=> $this->getting_started_content(),
				'post_name'		=> $this->getting_started_pageslug
			);

			$id = wp_insert_post (  apply_filters('pl_getting_started_page', $page) );
			
		}
		
		$templateID = pl_add_or_update_template( 'Getting Started', $this->getting_started_template(), 'PageLines getting started guide.');
		
		pl_set_page_template( $id, $templateID );
		
		return $id;
	}
	
	function getting_started_template(){
		
		$map = array(
			
			array(
				'object'	=> 'PLSectionArea',
				'settings'	=> array(
					'pl_area_bg' 	=> 'pl-dark-img',
					'pl_area_image'	=> '[pl_parent_url]/images/getting-started-mast-bg.jpg',
					'pl_area_pad'	=> '80px'
				),
				
				'content'	=> array(
					array(
						'object'	=> 'PLMasthead',
						'settings'	=> array(
							'pagelines_masthead_title'		=> 'Congratulations!',
							'pagelines_masthead_tagline'	=> 'You are up and running with PageLines DMS.',
							'pagelines_masthead_img'		=> '[pl_parent_url]/images/getting-started-pl-logo.png',
							'masthead_button_link_1'		=> home_url(),
							'masthead_button_text_1'		=> 'View Your Blog <i class="icon-angle-right"></i>',
						)
					),
				)
			),
			array(
				'content'	=> array(
					array(
						'object'	=> 'pliBox',
						'settings'	=> array(
							'ibox_array'	=> array(
								array(
									'title'	=> 'Quick Start',
									'text'	=> 'New to PageLines? Get started fast with PageLines DMS Quick Start guide...',
									'icon'	=> 'rocket',
									'link'	=> 'http://www.pagelines.com/quickstart/'
								),
								array(
									'title'	=> 'Forum',
									'text'	=> 'Have questions? We are happy to help, just search or post on PageLines Forum.',
									'icon'	=> 'comment',
									'link'	=> 'http://forum.pagelines.com/'
								),
								array(
									'title'	=> 'Docs',
									'text'	=> 'Time to dig in. Check out the Docs for specifics on getting your site finished.',
									'icon'	=> 'file-text',
									'link'	=> 'http://docs.pagelines.com/'
								),
							)
						)
					),
				)
			)
		); 
		
		return $map;
	}
	
	function getting_started_content(){
		
		ob_start(); 
		
		?>
		<h3>Welcome to DMS!</h3>
		<p>A cutting-edge drag & drop design management system for your website. <br/>Watch the video below for help getting started.</p>
		<iframe width='700' height='420' src='//www.youtube.com/embed/BracDuhEHls?rel=0&vq=hd720' frameborder='0' allowfullscreen></iframe>
		
		<?php 
		
		return ob_get_clean();
		
	}
	
	
}
