<?php



class PageLinesInstall{
	
	function __construct(){
		
		
		add_filter( 'pl_theme_default_settings', array($this, 'mod_default_settings') );
		
		// Add regions to settings
		add_filter( 'pl_theme_default_regions', array($this, 'mod_default_regions') );
		
		// Add theme templates when default templates are set
		add_filter( 'pl_default_templates', array($this, 'add_templates_at_default') );
		
		add_filter( 'pl_default_template_handler', array($this, 'default_template_handling') );
		
		// MUST COME AFTER FILTERS!
		$this->pagelines_check_install();
		
	}
	
	function pagelines_check_install() {

		$install = false;

		if( isset($_GET['pl-install-theme']) ){
			$install = true;
		}

		if( is_admin() ){
			global $pagenow;


			if( $pagenow == 'customize.php' || ( isset($_GET['activated'] ) && $pagenow == "themes.php" ) ){
				$install = true;
			
				
			}
		}
		
		if( $install == true ){
			$url = $this->run_installation_routine();

			wp_redirect( $url ); 

			exit;
		}
			
			
	}
	
	function run_installation_routine( $url = '' ){
		
		$settings = pl_get_global_settings(); 
		
		// Only sets defaults if they are null
		set_default_settings();
		
		if( ! $settings ){
			
			$this->load_page_templates();

			$this->apply_page_templates();

			

			// Publish New Templates
			$tpl_handler = new PLCustomTemplates;
			$tpl_handler->update_objects( 'publish' );
			
		}
		
		// Add Templates
		$id = $this->page_on_activation();
		
		
		// Redirect 
		$url = add_query_arg( 'pl-installed-theme', pl_theme_info('template'), get_permalink( $id ) );
		
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
	
	function apply_page_templates(){
		$mapping = $this->map_templates_to_pages();
		
		foreach( $mapping as $type => $tpl ){
			
			$id = pl_special_id( $type );
			pl_set_page_template( $id, $tpl, 'both' );
		}
	}
	
	// Override this to set templates on install
	function map_templates_to_pages(){
		return array();
	}
	
	function mod_default_regions( $defaults = array() ){

		return $this->global_region_map();
		
	}
	
	function mod_default_settings( $defaults ){
		
		return wp_parse_args( $this->set_global_options(), $defaults );
		
	}
	
	// Override this function in core/child themes
	// It will automatically load and/or update templates
	function page_templates( ){
		$templates = array(
			$this->template_welcome()
		);
				
		return $templates;
	}
	
	// Override this function in core/child themes
	// Use it to set global options on activation of theme
	function set_global_options( ){
		return array();
	}
	
	// Override this to change default templates for various types of pages
	function default_template_handling( $t ){
		return $t;
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
					'pl_area_parallax'	=> 'pl-parallax'
				),
				
				'content'	=> array(
					array(
						'object'	=> 'PLMasthead',
						'settings'	=> array(
							'pagelines_masthead_title'		=> __( 'Congratulations!', 'pagelines' ),
							'pagelines_masthead_tagline'	=> __( 'You are up and running with PageLines DMS.', 'pagelines' ),
							'pagelines_masthead_img'		=> '[pl_parent_url]/images/getting-started-pl-logo.png',
							'masthead_button_link_1'		=> home_url(),
							'masthead_button_text_1'		=> __( 'View Your Blog <i class="icon-angle-right"></i>', 'pagelines' ),
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
									'title'	=> __( 'Quick Start', 'pagelines' ),
									'text'	=> __( 'New to PageLines? Get started fast with PageLines DMS Quick Start guide...', 'pagelines' ),
									'icon'	=> 'rocket',
									'link'	=> 'http://www.pagelines.com/quickstart/'
								),
								array(
									'title'	=> __( 'Forum', 'pagelines' ),
									'text'	=> __( 'Have questions? We are happy to help, just search or post on PageLines Forum.', 'pagelines' ),
									'icon'	=> 'comment',
									'link'	=> 'http://forum.pagelines.com/'
								),
								array(
									'title'	=> __( 'Docs', 'pagelines' ),
									'text'	=> __( 'Time to dig in. Check out the Docs for specifics on creating your dream website.', 'pagelines' ),
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
			'post_title'	=> __( 'PageLines Getting Started', 'pagelines' ),
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
			
		
		pl_set_page_template( $id, $post_data['template'], 'both' );
		
		return $id;
	}
	
	
	
	function getting_started_content(){
		
		ob_start(); 
		
		?>
		<h3><?php _e( 'Welcome to DMS!', 'pagelines' ); ?>
		</h3>
		<p><?php _e( 'A cutting-edge drag & drop design management system for your website. <br/>Watch the video below for help getting started.', 'pagelines' ); ?></p>
		<iframe width='700' height='420' src='//www.youtube.com/embed/BracDuhEHls?rel=0&vq=hd720' frameborder='0' allowfullscreen></iframe>
		
		<?php 
		
		return ob_get_clean();
		
	}
	
	
}
