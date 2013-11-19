<?php

class PageLinesTemplates {

	var $map_option_slug = 'pl-template-map';

	function __construct( EditorTemplates $tpl ){

		$this->tpl = $tpl;
		
		$this->mode = pl_draft_mode() ? 'draft' : 'live';
	
		global $plpg; 
		$this->page = $plpg;
		
		$this->set = new PageLinesOpts;
	}

	function get_map( ){

		global $sections_handler;
		global $pl_custom_template; 
		
		$pl_custom_template = false;

		$map['header'] = $this->get_region( 'header' );
		$map['footer'] = $this->get_region( 'footer' );
		$map['template'] = $this->get_region( 'template' );
		
		$map = $sections_handler->replace_user_sections( $map );
		
		
		return $map;

	}
	
	

	function get_region( $region ){
		
		if($region == 'header' || $region == 'footer'){
			
			$map = $this->set->regions; 
				
		} elseif( $region == 'template' ){
			
			$map = false;
			
			$set = (is_page()) ? $this->set->local : $this->set->type;
	
			
			$tpl = ( isset($set['page-template']) ) ? $set['page-template'] : false;

			if( isset( $set['custom-map'] ) && is_array( $set['custom-map'] ) ){
				
				
				$map = $set['custom-map'];
				
				if( isset( $map[ $region ]['ctemplate'] ) ){
					
					global $pl_custom_template;
					
					$pl_custom_template = $map[ $region ]['ctemplate'];
					
					$map = $this->get_map_from_template_key( $map[ $region ]['ctemplate'] );
					
				
				}
					

			} elseif( is_page() && isset( $this->set->global['page-template']) ){
				
				$map = $this->get_map_from_template_key( $this->set->global['page-template'] ); 
				
			}
				
			
		}
		
		$region_map = ( $map && isset($map[ $region ]) ) ? $map[ $region ] : $this->default_region( $region );		

		return $region_map;
		
	}
	
	
	function get_map_from_template_key( $key ){

		$templates = $this->tpl->get_user_templates();
	
		$map = ( isset($templates[ $key ]) && isset($templates[ $key ]['map'] ) ) ? $templates[ $key ]['map'] : false;
			
		if($map)	
			return array( 'template' => $map );
		else 
			return false;
		
	}
	
	function default_region( $region ){
		
		
		
		if( $region == 'header' ){
			
			$d = array(
				array(
					'areaID'	=> 'HeaderArea',
					'content'	=> array( )
				)

			);
			
		} elseif( $region == 'footer' ){
			
			$d = array(
				array(
					'areaID'	=> 'FooterArea',
					'content'	=> array(
						array(
							'object' => 'SimpleNav'
						)
					)
				)

			);
			
		} elseif( $region == 'template' ){
			
			$d = array( $this->tpl->default_template() );
			
		}
		
		return $d;

		
	}

	function save_map_draft( $pageID, $typeID, $map, $mode){

		if(!$map)
			return; 
			
		// GLOBAL //
			$global_settings = pl_opt( PL_SETTINGS, pl_settings_default(), true );

			$global_settings['draft']['regions'] = array(
				'header' => $map['header'],
				'footer' => $map['footer']
			);

			pl_opt_update( PL_SETTINGS, $global_settings );

		// LOCAL OR TYPE //	
			$updateID = ($mode == 'local') ? $pageID : $typeID;
		
			$template_settings = pl_meta( $updateID, PL_SETTINGS, pl_settings_default());
		
			$new_settings = $template_settings;
		
			$new_settings['draft']['custom-map'] = array(
				'template' => $map['template']
			);

		if($new_settings != $template_settings){
			
			$new_settings['draft']['page-template'] = 'custom'; 
			
			pl_meta_update( $updateID, PL_SETTINGS, $new_settings );
			
			$local = 1;
		
		} else
			$local = 0;


		return array('local' => $local);
	}
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

		add_filter('pl_toolbar_config', array( $this, 'toolbar'));
		add_filter('pagelines_editor_scripts', array( $this, 'scripts'));

		add_action( 'admin_init', array( $this, 'admin_page_meta_box'));
		add_action( 'post_updated', array( $this, 'save_meta_options') );
		
		add_filter( 'pl_ajax_set_template', array( $this, 'set_template' ), 10, 2 );

	}

	function set_template( $response, $data ){
		$pageID = $data['pageID'];
		$typeID = $data['typeID'];
		$run = $data['run'];
		
		if ( $run == 'load' ){

			$metaID = (isset($data['templateMode']) && $data['templateMode'] == 'type') ? $typeID : $pageID;

			$response['loaded'] = $this->load_template( $metaID, $data['key'] );

		} elseif ( $run == 'update'){

			$key = ( isset($data['key']) ) ? $data['key'] : false;

			$template_map = $data['map']['template'];

			$response['tpl'] = $this->update_template( $key, $template_map, $postdata['settings'], $pageID );

		} elseif ( $run == 'delete'){

			$key = ( isset($data['key']) ) ? $data['key'] : false;

			$tpl->delete_template( $key );

		} elseif ( $run == 'save' ){

			$template_map = $data['map']['template'];
			$settings = $data['settings'];

			$name = (isset($data['template-name'])) ? $data['template-name'] : false;
			$desc = (isset($data['template-desc'])) ? $data['template-desc'] : '';

			if( $name )
				$response['key'] = $this->create_template($name, $desc, $template_map, $settings, $pageID);

		} elseif( $run == 'set_type' ){

			$field = 'page-template';
			$value = $data['value'];

			$previous_val = pl_local( $typeID, $field );

			if( $previous_val == $value )
				pl_local_update( $typeID, $field, false );
			else
				pl_local_update( $typeID, $field, $value );

			$response['result'] = pl_local( $typeID, $field );


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

		foreach( $this->get_user_templates() as $index => $template){


			$classes = array( sprintf('template_key_%s', $index) );

			$action_classes = array('x-item-actions'); 
			//$action_classes[] = ($index === $this->page->template) ? 'active-template' : '';
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

						<button class="btn btn-mini the-active-template"><?php _e( 'Active', 'pagelines' ); ?>
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
						<button class="btn btn-mini tpl-tag global-tag" title="Current Sitewide Default"><i class="icon-globe"></i></button>
						<button class="btn btn-mini tpl-tag posttype-tag" title="Current Post Type Default"><i class="icon-pushpin"></i></button>
					</div>
				</div>
				<div class="span6 list-desc">
					<?php echo $template['desc']; ?>
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
				<input type="text" id="template-name" name="template-name" required />

				<label for="template-desc"><?php _e( 'Template Description', 'pagelines' ); ?>
				</label>
				<textarea rows="4" id="template-desc" name="template-desc" ></textarea>
				
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

	function get_user_templates(){

		// get option
		$templates = pl_opt( $this->template_slug, $this->default_user_templates() );

		return $templates;

	}

	
	function get_template_data( $key ){
		
		$d = array(
			'name'	=> __( 'No Name', 'pagelines' ),
			'desc'	=> '', 
			'map'	=> array(),
			'settings'	=> array()
		); 
		
		
		$templates = $this->get_user_templates();
	
		if( isset($templates[ $key ]) ){
			
			$t = wp_parse_args($templates[ $key ], $d); 
			return $t;
			
		} else
			return false;
	}

	

	function load_template( $metaID, $templateID ){

		$t = $this->get_template_data( $templateID ); 

		$page_settings = pl_meta( $metaID, PL_SETTINGS, pl_settings_default() ); 

		$page_settings[ 'draft' ] = $t['settings'];
		
		$page_settings[ 'draft' ][ 'custom-map' ][ 'template' ] = $t['map'];
		
		$page_settings[ 'draft' ][ 'page-template' ] = $templateID;
		
		pl_meta_update($metaID, PL_SETTINGS, $page_settings);
		
		return $page_settings;

	}


	function create_template( $name, $desc, $map, $settings, $pageID ){

		$templates = $this->get_user_templates();
		
		$key = pl_create_id( $name );

		$new = array( $key => array(
				'name'		=> $name,
				'desc'		=> $desc,
				'map'		=> $map, 
				'settings'	=> $settings
				)
			);

		$templates = array_merge( $new, $templates );
		

		pl_opt_update( $this->template_slug, $templates );
		
		pl_local_update( $pageID, 'page-template', $key );
		
		return $key;

	}

	function update_template( $key, $template_map, $settings, $pageID){

		$templates = $this->get_user_templates();

		$templates[ $key ][ 'map' ] = $template_map;
		$templates[ $key ][ 'settings' ] = $settings;

		pl_opt_update( $this->template_slug, $templates );
		
		pl_local_update( $pageID, 'page-template', $key );
		
		return $key;

	}

	function delete_template( $key ){

		$templates = $this->get_user_templates();

		unset( $templates[$key] );

		pl_opt_update( $this->template_slug, $templates );

	}


	function default_template( $standard = false ){
	
		if( $this->page->type == '404_page' && !$standard){
			
				$t = array(
					'content'	=> array( array( 'object' => 'PageLinesNoPosts' ) )
				);
			
		} elseif( $this->page->type == 'page' && !$standard){
			
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




	function default_user_templates(){

		$t = array();

		$t[ 'default' ] = array(
				'name'	=> __( 'Default', 'pagelines' ),
				'desc'	=> __( 'Standard page configuration. (Content and Primary Sidebar.)', 'pagelines' ),
				'map'	=> array(
					'template' => $this->default_template( true )
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

			foreach($this->get_user_templates() as $index => $t){

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
