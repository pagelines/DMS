<?php

class PageLinesTemplates {

	var $map_option_slug = 'pl-template-map';

	function __construct( EditorTemplates $tpl ){

		$this->tpl = $tpl;
		
		$this->mode = pl_get_mode();
	
		global $plpg; 
		$this->page = $plpg;
		
		$this->set = new PageLinesOpts;
		
		add_filter( 'pl_load_page_settings', array( $this, 'add_template_settings_to_page') );
	}
	
	function add_template_settings_to_page( $page_settings ){
		
		global $pl_custom_template;
		
		$template_settings = ( ! empty( $pl_custom_template ) ) ? $pl_custom_template['settings'] : array();
		
		$new_page_settings = wp_parse_args( $template_settings, $page_settings );
		
		return $new_page_settings;
		
	}
	

	function get_map( ){

		global $sections_handler;
		global $pl_custom_template; 
		
		$pl_custom_template = false;

		$map['fixed'] = $this->get_region( 'fixed' );
		$map['header'] = $this->get_region( 'header' );
		$map['footer'] = $this->get_region( 'footer' );
		$map['template'] = $this->get_region( 'template' );
		
		
		
		$map = $sections_handler->replace_user_sections( $map );
		
		
		
		return $map;

	}
	
	

	function get_region( $region ){
		
		if( $region == 'header' || $region == 'footer' || $region == 'fixed' ){
			
			$map = $this->set->regions; 
				
		} elseif( $region == 'template' ){
			
			$map = false;
			
			$set = (is_page()) ? $this->set->local : $this->set->type;

				
			
			if( isset( $set['custom-map'] ) && is_array( $set['custom-map'] ) ){
				
			
				$map = $set['custom-map'];
				
				
	
				if( isset( $map[ $region ]['ctemplate'] ) ){
					
					$key = $map[ $region ]['ctemplate'];
					
					global $pl_custom_template;
					
					$pl_custom_template = $this->tpl->handler->retrieve( $key ); 
					
					if( $pl_custom_template && !empty($pl_custom_template)){
						
						$pl_custom_template['key'] = $key;

						$map[ $region ] = $pl_custom_template['map'];
						
					} else 
						$map = false;
					
				//	plprint( $pl_custom_template );
					
				}
					

			} elseif( is_page() && isset( $this->set->global['page-template']) ){
				
				$key = $this->set->global['page-template']; 
				$map = $this->tpl->handler->retrieve_field( $key, 'map'); 
				
			}
				
			
		}
		
		
		
		$region_map = ( $map && isset($map[ $region ]) ) ? $map[ $region ] : $this->default_region( $region );		

		return $region_map;
		
	}
	
	function upgrade_navbar_settings(){
		
		$settings = array(
			'navbar_theme'			=> pl_setting('fixed_navbar_theme' ),
			'navbar_alignment'		=> pl_setting('fixed_navbar_alignment' ),
			'navbar_hidesearch'		=> pl_setting('fixed_navbar_hidesearch' ),
			'navbar_menu'			=> pl_setting('fixed_navbar_menu' ),
			'navbar_enable_hover'	=> pl_setting('fixed_navbar_enable_hover' ),
			'navbar_logo'			=> pl_setting('navbar_logo' ),
		);
		
		return $settings;
	}
	

	function default_region( $region ){
		
		$d = array();
		
		if( $region == 'header' ){
			
			$d = array( array( 'content' => array( ) ) );
			
		} elseif( $region == 'fixed' ){
			
			$d = array( 
					array( 
						'object' 	=> 'PLNavBar',
						'settings'	=> $this->upgrade_navbar_settings()
					) 
				);
			
		} elseif( $region == 'footer' ){
			
			$d = array(
				array(
					'content'	=> array(
						array(
							'object' => 'SimpleNav'
						)
					)
				)

			);
			
		} elseif( $region == 'template' ){
			
			$d = array( pl_default_template() );
			
		}
		
		return $d;

		
	}

	// function save_map_draft( $pageID, $typeID, $map, $mode){
	// 
	// 	if(!$map)
	// 		return; 
	// 		
	// 	// GLOBAL //
	// 		$global_settings = pl_opt( PL_SETTINGS, pl_settings_default(), true );
	// 
	// 		$global_settings['draft']['regions'] = array(
	// 			'header' => $map['header'],
	// 			'footer' => $map['footer'],
	// 			'fixed' => $map['fixed']
	// 		);
	// 
	// 		pl_opt_update( PL_SETTINGS, $global_settings );
	// 
	// 	// LOCAL OR TYPE //	
	// 		$updateID = ($mode == 'local') ? $pageID : $typeID;
	// 	
	// 		$template_settings = pl_meta( $updateID, PL_SETTINGS, pl_settings_default());
	// 	
	// 		$new_settings = $template_settings;
	// 	
	// 		$new_settings['draft']['custom-map'] = array(
	// 			'template' => $map['template']
	// 		);
	// 
	// 	if($new_settings != $template_settings){
	// 		
	// 		$new_settings['draft']['page-template'] = 'custom'; 
	// 		
	// 		pl_meta_update( $updateID, PL_SETTINGS, $new_settings );
	// 		
	// 		$local = 1;
	// 	
	// 	} else
	// 		$local = 0;
	// 
	// 
	// 	return array('local' => $local);
	// }
}

class EditorTemplates {

	var $template_slug = 'pl-user-templates';
	var $default_template_slug = 'pl-default-tpl';
	var $map_option_slug = 'pl-template-map';
	var $template_id_slug = 'pl-template-id';


	var $page_template_slug = 'pl-page-template'; 

	function __construct( ){
	
		global $plpg;
		$this->page = $plpg;

		$this->default_type_tpl = ($plpg && $plpg != '') ? pl_local( $plpg->typeid, 'page-template' ) : false;

		$this->default_global_tpl = pl_global( 'page-template' );

		$this->default_tpl = ( $this->default_type_tpl ) ? $this->default_type_tpl : $this->default_global_tpl;

		$this->url = PL_PARENT_URL . '/editor';

		$this->handler = new PLCustomTemplates;

		add_filter('pl_toolbar_config', array( $this, 'toolbar'));
		add_filter('pagelines_editor_scripts', array( $this, 'scripts'));

		add_action( 'admin_init', array( $this, 'admin_page_meta_box'));
		add_action( 'post_updated', array( $this, 'save_meta_options') );
		
		add_filter( 'pl_ajax_set_template', array( $this, 'set_template' ), 10, 2 );
		
	

	}
	
	
	function set_template( $response, $data ){
		
		$run = $data['run'];
		
		if ( $run == 'update'){
		
			$response['key'] = $this->handler->update( $data['key'], $data['config'] );

		} elseif ( $run == 'delete'){

			$response['key'] = $this->handler->delete( $data['key'] );

		} elseif ( $run == 'create' ){

			$response['key'] = $this->handler->create( $data['config'] );

		} elseif( $run == 'set_global' ){

			$field = 'page-template';
			$value = $data['value'];

			$previous_val = pl_global( $field );

			if($previous_val == $value)
				pl_global_update( $field, false );
			else
				pl_global_update( $field, $value );


			$response['result'] = pl_global( $field );

		}
		
		
		
		return $response;
	}

	function scripts(){
		wp_enqueue_script( 'pl-js-mapping', $this->url . '/js/pl.mapping.js', array('jquery'), PL_CORE_VERSION, true);
		wp_enqueue_script( 'pl-js-templates', $this->url . '/js/pl.templates.js', array( 'jquery' ), PL_CORE_VERSION, true );
	}

	function toolbar( $toolbar ){
		
		
		$toolbar['page-setup'] = array(
			'name'	=> __( 'Page Setup', 'pagelines' ),
			'icon'	=> 'icon-file-text',
			'pos'	=> 30,
			'panel'	=> array(
				
				'heading2'	=> __( "Page Setup", 'pagelines' ),
				'tmp_load'	=> array(
					'name'	=> __( 'Templates', 'pagelines' ),
					'call'	=> array( $this, 'user_templates'),
					'icon'	=> 'icon-copy',
					'filter' => '*'
				),
				'tmp_save'	=> array(
					'name'	=> __( 'Page Controls', 'pagelines' ),
					'call'	=> array( $this, 'page_settings'),
					'icon'	=> 'icon-wrench'
				),
			)

		);

		return $toolbar;
	}

	function user_templates(){
		$slug = $this->default_template_slug;
		$this->xlist = new EditorXList;
		$templates = '';
		$list = '';
		$tpls = pl_meta( $this->page->id, $this->map_option_slug, pl_settings_default());

		$custom_template_handler = new PLCustomTemplates;

		foreach( $custom_template_handler->get_all() as $index => $template){


			$classes = array( sprintf('template_key_%s', $index) );

			$action_classes = array('x-item-actions'); 
			
			global $pl_custom_template; 

			if(! empty( $pl_custom_template ) ){
				$action_classes[] = ($index === $pl_custom_template['key']) ? 'active-template' : '';
			}
			
			$action_classes[] = ($index === $this->default_global_tpl) ? 'active-global' : '';
			$action_classes[] = ($index === $this->default_type_tpl && !$this->page->is_special()) ? 'active-type' : '';
			

			ob_start();
			?>
			
			<div class="pl-list-row row pl-template-row <?php echo join(' ', $classes); ?>" data-key="<?php echo $index;?>">
				
				<div class="span3 list-head">
					<div class="list-title"><?php echo stripslashes( $template['name'] ); ?></div>
					
				</div>
				<div class="span3 list-actions">
					<div class="<?php echo join(' ', $action_classes);?>">

						<button class="btn btn-mini btn-primary load-template"><?php _e( 'Load', 'pagelines' ); ?>
						</button>

						<button class="btn btn-mini btn-important the-active-template"><?php _e( 'Active', 'pagelines' ); ?>
						</button>

						<div class="btn-group dropup">
						  <a class="btn btn-mini dropdown-toggle actions-toggle" data-toggle="dropdown" href="#">
						    <?php _e( 'Actions', 'pagelines' ); ?>
						    	<i class="icon-caret-down"></i>
						  </a>
							<ul class="dropdown-menu">
								<li ><a class="update-template">
								<i class="icon-edit"></i> <?php _e( 'Update Template with Current Configuration', 'pagelines' ); ?>

								</a></li>

								<li><a class="set-tpl" data-run="global">
								<i class="icon-globe"></i> <?php _e( 'Set as Page Global Default', 'pagelines' ); ?>

								</a></li>

								<li><a class="delete-template">
								<i class="icon-remove"></i> <?php _e( 'Delete This Template', 'pagelines' ); ?>

								</a></li>

							</ul>
						</div>
						<button class="btn btn-mini tpl-tag global-tag tt-top" title="Current Sitewide Default"><i class="icon-globe"></i></button>
						<button class="btn btn-mini tpl-tag posttype-tag tt-top" title="Current Post Type Default"><i class="icon-pushpin"></i></button>
					</div>
				</div>
				<div class="span6 list-desc">
					<?php echo stripslashes( $template['desc'] ); ?>
				</div>
			</div>

			<?php

			$list .= ob_get_clean();




		}

		


		ob_start(); 
		?>

		<form class="opt standard-form form-save-template">
			<fieldset>
				<h4>Save Current Page As New Template</h4>
				</span>
				<label for="template-name"><?php _e( 'Template Name (required)', 'pagelines' ); ?>
				</label>
				<input type="text" id="template-name" name="name" required />

				<label for="template-desc"><?php _e( 'Template Description', 'pagelines' ); ?>
				</label>
				<textarea rows="4" id="template-desc" name="desc" ></textarea>
				
				<button type="submit" class="btn btn-primary btn-save-template"><?php _e( 'Save New Template', 'pagelines' ); ?>
				</button>
			</fieldset>
		</form>

		<?php
		
		$form = ob_get_clean();
		
		printf('<div class="row"><div class="span7"><div class="pl-list-contain">%s</div></div><div class="span5">%s</div></div>', $list, $form);
	}

	function page_settings(){

		?>

		<form class="opt standard-form form-save-template">
			<fieldset>
				<span class="help-block">
					<?php _e( 'Fill out this form and the current template will be saved for use throughout your site.', 'pagelines' ); ?>
					<br/>
					<?php _e( "<strong>Note:</strong> Both the current page's local settings and section configurations will be saved.", 'pagelines' ); ?>
					
				</span>
				<label for="template-name"><?php _e( 'Template Name (required)', 'pagelines' ); ?>
				</label>
				<input type="text" id="template-name" name="template-name" required />

				<label for="template-desc"><?php _e( 'Template Description', 'pagelines' ); ?>
				</label>
				<textarea rows="4" id="template-desc" name="template-desc" ></textarea>
				
				<button type="submit" class="btn btn-primary btn-save-template"><?php _e( 'Save New Template', 'pagelines' ); ?>
				</button>
			</fieldset>
		</form>

		<?php

	}
	
	
	function admin_page_meta_box(){
		if(pl_deprecate_v2())
			remove_meta_box( 'pageparentdiv', 'page', 'side' );
			
		add_meta_box('specialpagelines', __( 'DMS Page Setup', 'pagelines' ), array( $this, 'page_attributes_meta_box'), 'page', 'side');

	}

	/* 
	 * Used for WordPress Post Saving of PageLines Template
	 */ 
	function save_meta_options( $postID ){
		$post = $_POST;
		if((isset($post['update']) || isset($post['save']) || isset($post['publish']))){


			$user_template = (isset($post['pagelines_template'])) ? $post['pagelines_template'] : '';

			if($user_template != ''){

				$set = pl_meta($postID, PL_SETTINGS);
				
				$set['draft']['page-template'] = $user_template; 
				$set['live']['page-template'] = $user_template; 
				
				pl_meta_update($postID, PL_SETTINGS, $set);
			}


		}
	}
	/* 
	 * Adds PageLines Template selector when creating page/post
	 */
	function page_attributes_meta_box( $post ){
		$post_type_object = get_post_type_object($post->post_type);

		///// CUSTOM PAGE TEMPLATE STUFF /////

			$options = '<option value="">Select Template</option>';
			
			$set = pl_meta($post->ID, PL_SETTINGS);

			$current = ( is_array( $set ) && isset( $set['live']['page-template'] ) ) ? $set['live']['page-template'] : '';

			$custom_template_handler = new PLCustomTemplates;

			foreach( $custom_template_handler->get_all() as $index => $t){

				$sel = '';
				
				$template = explode( ' ', $t['name'] );
				
				$sel = ( $current === strtolower( $template[0] ) ) ? 'selected' : '';
				
				$options .= sprintf('<option value="%s" %s>%s</option>', $index, $sel, $t['name']);
			}

			printf('<p><strong>%1$s</strong></p>', __('Load PageLines Template', 'pagelines'));

			printf('<select name="pagelines_template" id="pagelines_template">%s</select>', $options);

		///// END TEMPLATE STUFF /////


		if ( $post_type_object->hierarchical ) {
			$dropdown_args = array(
				'post_type'        => $post->post_type,
				'exclude_tree'     => $post->ID,
				'selected'         => $post->post_parent,
				'name'             => 'parent_id',
				'show_option_none' => __('(no parent)', 'pagelines' ),
				'sort_column'      => 'menu_order, post_title',
				'echo'             => 0,
			);

			$dropdown_args = apply_filters( 'page_attributes_dropdown_pages_args', $dropdown_args, $post );
			$pages = wp_dropdown_pages( $dropdown_args );
			if ( ! empty($pages) ) {
				printf('<p><strong>%1$s</strong></p>', __( 'Parent Page', 'pagelines' ) );
				echo $pages;
			}
		}

		printf('<p><strong>%1$s</strong></p>', __( 'Page Order', 'pagelines' ) );
		printf('<input name="menu_order" type="text" size="4" id="menu_order" value="%s" /></p>', esc_attr($post->menu_order) );
	}
}

class PLCustomTemplates extends PLCustomObjects{
	
	function __construct(  ){
		
		$this->slug = 'pl-user-templates';
		
		$this->objects = $this->get_all();
		
	}
	
	function default_objects(){

		$t = array();

		$t[ 'default' ] = array(
				'name'	=> __( 'Default', 'pagelines' ),
				'desc'	=> __( 'Standard page configuration. (Content and Primary Sidebar.)', 'pagelines' ),
				'map'	=> array(
					'template' => pl_default_template( true )
				)
			);

		$t[ 'feature' ] = array(
			'name'	=> __( 'Feature Template', 'pagelines' ),
			'desc'	=> __( 'A page template designed to quickly and concisely show off key features or points. (RevSlider, iBoxes, Flipper)', 'pagelines' ),
			'map'	=> array(
				array(
					'object'	=> 'plRevSlider',
				),
				array(
					'content'	=> array(
						array(
							'object'	=> 'pliBox',

						),
						array(
							'object'	=> 'PageLinesFlipper',

						),
					)
				)
			)
		);

		$t[ 'landing' ] = array(
				'name'	=> __( 'Landing Page', 'pagelines' ),
				'desc'	=> __( 'A simple page design with highlight section and postloop (content).', 'pagelines' ),
				'map'	=> array(
					'template' => array(
						'area'	=> 'TemplateAreaID',
						'content'	=> array(
							array(
								'object'	=> 'PageLinesHighlight',
							),
							array(
								'object'	=> 'PageLinesPostLoop',
								'span'		=> 8, 
								'offset'	=> 2
							),

						)
					)
				)
		);

		return $t;
	}
}

function pl_default_template( $standard = false ){

	global $plpg;

	if( $plpg->type == '404_page' && ! $standard){
		
			$t = array(
				'content'	=> array( array( 'object' => 'PageLinesNoPosts' ) )
			);
		
	} elseif( $plpg->type == 'page' && ! $standard){
		
		$t = array(
			'content'	=> array(
				array(
					'object'	=> 'PageLinesPostLoop',
					'span' 		=> 8,
					'offset'	=> 2
				)
			)
		);
		
	} else {
		
		$t = array(
			'name'	=> 'Content Area',
			'class'	=> 'std-content',
			'content'	=> array(
				array(
					'object'	=> 'PLColumn',
					'span' 	=> 8,
					'content'	=> array(
						array(
							'object'	=> 'PageLinesPostLoop'
						),
						array(
							'object'	=> 'PageLinesComments'
						),
					)
				),
				array(
					'object'	=> 'PLColumn',
					'span' 	=> 4,
					'content'	=> array(
						array(
							'object'	=> 'PLRapidTabs'
						),
						array(
							'object'	=> 'PrimarySidebar'
						),
					)
				),
			)
		);
		
	}
	

	return $t;

}

function pl_add_or_update_template( $name, $map, $desc = '' ){
	$tpls = new PLCustomTemplates;
	
	$args = array(
		'name'	=> $name, 
		'desc'	=> $desc, 
		'map'	=> $map
	); 
	
	$key = $tpls->create( $args );
	
	return $key;
}

function pl_set_page_template( $metaID, $key ){
	
	// set local meta
	
	$map = array();
	
	$map['template']['ctemplate'] = $key;
	
	pl_local_update( $metaID, 'custom-map', $map );

	
}


