<?php



class PageLinesInstall{
	
	function __construct(){
		
		$this->pagelines_check_install();
		
		add_filter( 'pl_theme_activate_url', array($this, 'run_installation_routine'));
		
		add_filter( 'pl_theme_default_settings', array($this, 'mod_default_settings') );
		
		// Add regions to settings
		add_filter( 'pl_theme_default_regions', array($this, 'mod_default_regions') );
		
		// Add theme templates when default templates are set
		add_filter( 'pl_default_templates', array($this, 'add_templates_at_default') );
		
	}
	
	function pagelines_check_install() {

		if( ! is_admin() )
			return;

		global $pagenow;
		

		if( $pagenow == 'customize.php' || ( isset($_GET['activated'] ) && $pagenow == "themes.php" ) ){
			$url = $this->run_installation_routine();
			
			wp_redirect( $url ); 

			exit;
		}
			
			
	}
	
	function run_installation_routine( $url = '' ){
		
		$this->load_page_templates();
		
		// Add Templates
		$id = $this->page_on_activation();
		
		// Redirect 
		$url = add_query_arg( 'plinstall', pl_theme_info('template'), get_permalink( $id ) );
		
		return $url;
	
	}
	
	function add_templates_at_default( $tpls ){
	
		$tpls = array_merge( $this->page_templates( ), $tpls );
	
		return $tpls;
		
	}
	
	function load_page_templates(){
		
		$page_templates = $this->page_templates();
		
		foreach( $page_templates as $tpl ){
			$templateID = pl_add_or_update_template( $tpl );
		}
		
	}
	
	function mod_default_regions( $defaults = array() ){

		return $this->global_region_map();
		
	}
	
	function mod_default_settings( $defaults ){
		
		return wp_parse_args( $this->set_global_options(), $defaults );
		
	}
	
	// Override this function in core/child themes
	function page_templates( ){
		$templates = array(
			$this->template_welcome()
		);
				
		return $templates;
	}
	
	// Override this function in core/child themes
	function set_global_options( ){
		return array();
	}
	
	// Override this function in core/child themes
	function global_region_map(){
		
		$map = array(
			'header'	=> array(), 
			'footer'	=> array(
				array(
					'content'	=> array(
						array( 'object'	=> 'SimpleNav' ),
						array( 'object'	=> 'PLWatermark' )
					)
				)
			),
			'fixed'	=> array(
				array( 'object'	=> 'PLNavBar' )
			)
		);
		
		return $map;
		
	}
	
	// Override this function in core/child themes
	function activation_page_data(){

		return array();
	}
	
	
	function template_welcome(){
		
		$template['name'] = 'Welcome';
		
		$template['desc'] = 'Getting started guide &amp; template.';
		
		$template['map'] = array(
			
			array(
				'object'	=> 'PLSectionArea',
				'settings'	=> array(
					'pl_area_bg' 		=> 'pl-dark-img',
					'pl_area_image'		=> '[pl_parent_url]/images/getting-started-mast-bg.jpg',
					'pl_area_pad'		=> '80px',
					'pl_area_parallax'	=> 1
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
									'text'	=> 'Time to dig in. Check out the Docs for specifics on creating your dream website.',
									'icon'	=> 'file-text',
									'link'	=> 'http://docs.pagelines.com/'
								),
							)
						)
					),
				)
			)
		); 
		
		return $template;
	}
	
	
	function page_on_activation( $templateID = 'welcome' ){
		
		global $user_ID;
		
		$data = $this->activation_page_data();
		
		$page = array(
			'post_type'		=> 'page',
			'post_status'	=> 'draft',
			'post_author'	=> $user_ID,
			'post_title'	=> 'PageLines Getting Started',
			'post_content'	=> $this->getting_started_content(),
			'post_name'		=> 'pl-getting-started',
			'template'		=> 'welcome',
		);
		
		$post_data = wp_parse_args( $data, $page );
		
		// Check or add page (leave in draft mode)
		$pages = get_pages( array( 'post_status' => 'draft' ) );
		$page_exists = false;
		foreach ($pages as $page) { 
			
			$name = $page->post_name;
			
			if ( $name == $post_data['post_name'] ) { 
				$page_exists = true;
				$id = $page->ID;
			}
			 
		}
		
		if( ! $page_exists )
			$id = wp_insert_post(  $post_data );
			
		
		pl_set_page_template( $id, $post_data['template'] );
		
		return $id;
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
