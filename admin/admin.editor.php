<?php


class EditorAdmin {
	
	function __construct(){
		
		add_action( 'pagelines_options_dms_less', array( $this, 'dms_tools_less') );
		add_action( 'pagelines_options_dms_scripts', array( $this, 'dms_scripts_template') );
		add_action( 'pagelines_options_dms_intro', array( $this, 'dms_intro') );
		add_action( 'pagelines_options_dms_debug', array( $this, 'dms_debug') );
	}
	
	function admin_array(){

		$d = array(
			'pagelines_settings' => array( 
				'title'		=> __( 'PageLines Settings', 'pagelines' ),
				'groups'	=> array(
					array(
						'title'	=> __( '<i class="pl-di pl-di-images"></i> Site Images', 'pagelines' ), 
						'opts'	=> array(
							array(
								'key'			=> 'pagelines_favicon',
								'label'			=> __( 'Upload Favicon (32px by 32px)', 'pagelines' ),
								'type' 			=> 	'image_upload',
								'imgsize' 			=> 	'16',
								'extension'		=> 'ico,png', // ico support
								'title' 		=> 	__( 'Favicon Image', 'pagelines' ),
								'help' 			=> 	__( 'Enter the full URL location of your custom <strong>favicon</strong> which is visible in browser favorites and tabs.<br/> <strong>Must be .png or .ico file - 32px by 32px</strong>.', 'pagelines' ),
								'default'		=>  '[pl_parent_url]/images/default-favicon.png'
							),


							array(
								'key'			=> 'pl_login_image',
								'type' 			=> 	'image_upload',
								'col'			=> 2,
								'label'			=> __( 'Upload Login Image (80px Height)', 'pagelines' ),
								'imgsize' 			=> 	'80',
								'sizemode'		=> 'height',
								'title' 		=> __( 'Login Page Image', 'pagelines' ),
								'default'		=> '[pl_parent_url]/images/default-login-image.png',
								'help'			=> __( 'This image will be used on the login page to your admin. Use an image that is approximately <strong>80px</strong> in height.', 'pagelines' )
							),

							array(
								'key'			=> 'pagelines_touchicon',
								'col'			=> 3,
								'label'			=> __( 'Upload Touch Image (144px by 144px)', 'pagelines' ),
								'type' 			=> 	'image_upload',
								'imgsize' 			=> 	'72',
								'title' 		=> __( 'Mobile Touch Image', 'pagelines' ),
								'default'		=> '[pl_parent_url]/images/default-touch-icon.png',
								'help'			=> __( 'Enter the full URL location of your Apple Touch Icon which is visible when your users set your site as a <strong>webclip</strong> in Apple Iphone and Touch Products. It is an image approximately 144px by 144px in either .jpg, .gif or .png format.', 'pagelines' )
							),
						)
					),
					array(
						'title'	=> __( '<i class="pl-di pl-di-networking"></i> Layout Handling', 'pagelines' ), 
						'opts'	=> array(
							array(
								'key'		=> 'layout_opts',
								'type' 		=> 'multi',
								'title' 	=> __( 'Layout Configuration', 'pagelines' ),
								'opts' 		=> array(
									array(
										'key'		=> 'layout_mode',
										'type' 		=> 'select',
										'label' 	=> __( 'Select Content Width Mode', 'pagelines' ),
										'title' 	=> __( 'Layout Mode', 'pagelines' ),
										'opts' 		=> array(
											'pixel' 	=> array('name' => __( 'Pixel Width Based Layout', 'pagelines' )),
											'percent' 	=> array('name' => __( 'Percentage Width Based Layout', 'pagelines' ))
										),
										'default'	=> 'pixel',
									),
									array(
										'key'		=> 'layout_display_mode',
										'type' 		=> 'select',
										'label' 	=> __( 'Select Layout Display', 'pagelines' ),
										'title' 	=> __( 'Display Mode', 'pagelines' ),
										'opts' 		=> array(
											'display-full' 		=> array('name' => __( 'Full Width Display', 'pagelines' )),
											'display-boxed' 	=> array('name' => __( 'Boxed Display', 'pagelines' ))
										),
										'default'	=> 'display-full',
									),
								),
							),
						)
					), 
					array(
						'title'	=> __( '<i class="pl-di pl-di-networking"></i> Navigation', 'pagelines' ), 
						'opts'	=> array(
							array(

								'key'		=> 'layout_navigations',
								'col'		=> 2,
								'type' 		=> 'multi',
								'title' 	=> __( 'Default Navigation Setup', 'pagelines' ),
								'help'	 	=> __( 'These will be used in mobile menus and optionally other places throughout your site.', 'pagelines' ),
								'opts'	=> array(
									array(
										'key'		=> 'primary_navigation_menu',
										'type' 		=> 'select_menu',
										'label' 	=> __( 'Primary Navigation Menu', 'pagelines' ),


									),
									array(
										'key'		=> 'secondary_navigation_menu',
										'type' 		=> 'select_menu',
										'label' 	=> __( 'Secondary Navigation Menu', 'pagelines' ),

									),

									array(
										'key'		=> 'nav_dropdown_bg',
										'type' 		=> 'select',
										'label' 	=> __( 'Standard Nav Dropdown Background', 'pagelines' ),
										'default'	=> 'dark',
										'opts' 		=> array(
											'dark' 		=> array('name' => __( 'Dark Dropdowns', 'pagelines' )),
											'light' 	=> array('name' => __( 'Light Dropdowns', 'pagelines' ))
										),
									),

									array(
										'key'		=> 'nav_dropdown_toggle',
										'type' 		=> 'select',
										'label' 	=> __( 'Standard Nav Dropdown Toggle', 'pagelines' ),
										'default'	=> 'hover',
										'opts' 		=> array(
											'hover' 	=> array('name' => __( 'On Hover', 'pagelines' )),
											'click' 	=> array('name' => __( 'On Click', 'pagelines' ))
										),
									),

								),
							)
						)
					),
					array(
						'title'	=> __( '<i class="pl-di pl-di-share"></i> Social and Local', 'pagelines' ), 
						'opts'	=> array(
							array(
								'key'		=> 'karma_icon',
								'label'		=> __( 'Select icon for Social Counter', 'pagelines' ),
								'default'	=> 'sun',
								'title'		=> 'Social Counter',
								'type'		=> 'select_icon'
							),
							array(
								'key'		=> 'twittername',
								'type' 		=> 'text',
								'label' 	=> __( 'Your Twitter Username', 'pagelines' ),
								'title' 	=> __( 'Twitter Integration', 'pagelines' ),
								'help' 		=> __( 'This places your Twitter feed on the site. Leave blank if you want to hide or not use.', 'pagelines' )
							),
							array(
								'key'		=> 'fb_multi',
								'type'		=> 'multi', 
								'col'		=> 2,
								'title'		=> 'Facebook',
								'opts'		=> array(
									array(
										'key'		=> 'facebook_name',
										'type' 		=> 'text',
										'label' 	=> __( 'Your Facebook Page Name', 'pagelines' ),
										'title' 	=> __( 'Facebook Page', 'pagelines' ),
										'help' 		=> __( 'Enter the name component of your Facebook page URL. (For example, what comes after the facebook url: www.facebook.com/[name])', 'pagelines' )
									),
								)
							),

							array(
								'key'		=> 'site-hashtag',
								'type' 		=> 'text',
								'label' 	=> __( 'Your Website Hashtag', 'pagelines' ),
								'title' 	=> __( 'Website Hashtag', 'pagelines' ),
								'help'	 	=> __( 'This hashtag will be used in social media (e.g. Twitter) and elsewhere to create feeds.', 'pagelines' )
							),
						)
					)
				)
			), 
			'pagelines_scripts' => array( 
				'title'		=> __( 'Scripts', 'pagelines' ),
				'groups'	=> array(
					array(
						'title'	=> __( 'Website CSS and Scripts', 'pagelines' ),
						'desc'	=> __( 'Below are secondary fallbacks to the DMS code editors. You may need these if you create errors or issues on the front end.', 'pagelines' ),
						'opts'	=> array(
							'tools'		=> array(
								'type'		=> 'dms_less',
								'title'		=> __( 'DMS LESS Fallback', 'pagelines' ),
							),
							'tools2'		=> array(
								'type'		=> 'dms_scripts',
								'title'		=> __( 'DMS Header Scripts Fallback', 'pagelines' ),
							),
						)
					)
				)
			),
			'pagelines_import_export' => array( 
				'title'		=> __( 'Import / Export', 'pagelines' ),
				'groups'	=> array(
								
					array(
						'title'	=> __( 'Website Region Controls', 'pagelines' ),
						'desc'	=> __( 'Enable or disable various regions of your website from view.', 'pagelines' ),
						'opts'	=> array(
							'debug'		=> array(
								'type'	=> 'dms_debug',
								'title'	=> __( 'Enable DMS Debug Mode.', 'pagelines' )
							)
						)
					),
					array(
						'title'	=> __( 'Debug Options', 'pagelines' ),
						'opts'	=> array(
							array(
									'key'		=> 'enable_debug',
									'type'		=> 'check',
									'label'		=> __( 'Enable debug?', 'pagelines' ),
									'title'		=> __( 'PageLines debug', 'pagelines' ),
									'help'		=> sprintf( __( 'This information can be useful in the forums if you have a problem. %s', 'pagelines' ),
												   sprintf( '%s', ( pl_setting( 'enable_debug' ) ) ?
												   sprintf( '<br /><a href="%s" target="_blank">Click here</a> for your debug info.', site_url( '?pldebug=1' ) ) : '' ) )								  
							),
							array(
									'key'		=> 'disable_less_errors',
									'default'	=> false,
									'type'		=> 'check',
									'label'		=> __( 'Disable Error Notices?', 'pagelines' ),
									'title'		=> __( 'Less Notices', 'pagelines' ),
									'help'		=> __( 'Disable any error notices sent to wp-admin by the less system', 'pagelines' ),								  
							)
						)
					)
				)
			),
			'pagelines_resets' => array( 
				'title'		=> __( 'Resets', 'pagelines' ),
				'groups'	=> array(
					array(
						'type'	=> 'multi',
						'col'	=> 3,
						'title'	=> __( 'Resets', 'pagelines' ),
						'opts'	=> array(
							array(
									'key'		=> 'reset_global',
									'type'		=> 'action_button',
									'classes'	=> 'btn-important',
									'label'		=> __( '<i class="icon icon-undo"></i> Reset Global Settings', 'pagelines' ),
									'title'		=> __( 'Reset Global Site Settings', 'pagelines' ),
									'help'		=> __( "Use this button to reset all global settings to their default state. <br/><strong>Note:</strong> Once you've completed this action, you may want to publish these changes to your live site.", 'pagelines' )
							),
							array(
									'key'		=> 'reset_local',
									'type'		=> 'action_button',
									'classes'	=> 'btn-important',
									'label'		=> __( '<i class="icon icon-undo"></i> Reset Current Page Settings', 'pagelines' ),
									'title'		=> __( 'Reset Current Page Settings', 'pagelines' ),
									'help'		=> __( "Use this button to reset all settings on the current page back to their default state. <br/><strong>Note:</strong> Once you've completed this action, you may want to publish these changes to your live site.", 'pagelines' )
							),
							array(
									'key'		=> 'reset_type',
									'type'		=> 'action_button',
									'classes'	=> 'btn-important',
									'label'		=> __( '<i class="icon icon-undo"></i> Reset Current Post Type Settings', 'pagelines' ),
									'title'		=> __( 'Reset Current Post Type Settings', 'pagelines' ),
									'help'		=> __( "Use this button to reset all settings on the current post type back to their default state. <br/><strong>Note:</strong> Once you've completed this action, you may want to publish these changes to your live site.", 'pagelines' )
							),
							array(
									'key'		=> 'reset_cache',
									'col'		=> 2,
									'type'		=> 'action_button',
									'classes'	=> 'btn-info',
									'label'		=> __( '<i class="icon icon-trash"></i> Flush Caches', 'pagelines' ),
									'title'		=> __( 'Clear all CSS/LESS cached data.', 'pagelines' ),
									'help'		=> __( "Use this button to purge the stored LESS/CSS data. This will also clear cached pages if wp-super-cache or w3-total-cache are detected.", 'pagelines' )
							),
						)
					),
				)
			),
			
			'pagelines_advanced' => array( 
				'title'		=> __( 'Advanced', 'pagelines' ),
				'groups'	=> array(
								
					array(
						'title'	=> __( '<i class="pl-di pl-di-regions"></i> Region Control', 'pagelines' ),
						
						'opts'	=> array(
							array(
								'title'	=> 'Region Visibility',
								'type'	=> 'multi',
								'help'	=> __( 'Enable or disable various regions of your website from view.', 'pagelines' ),
								'opts'	=> array(
									array(
											'key'		=> 'region_disable_fixed',
											'type'		=> 'check',
											'label'		=> __( 'Disable Fixed Region?', 'pagelines' ),							  
									),
									array(
											'key'		=> 'region_disable_header',
											'type'		=> 'check',
											'label'		=> __( 'Disable Header Region?', 'pagelines' ),							  
									),
									array(
											'key'		=> 'region_disable_footer',
											'type'		=> 'check',
											'label'		=> __( 'Disable Footer Region?', 'pagelines' ),								  
									),
								)
							)
							
						)
					),
					array(
						'title'	=> __( 'Debug Options', 'pagelines' ),
						'opts'	=> array(
							array(
									'key'		=> 'enable_debug',
									'type'		=> 'check',
									'label'		=> __( 'Enable debug?', 'pagelines' ),
									'title'		=> __( 'PageLines debug', 'pagelines' ),
									'help'		=> sprintf( __( 'This information can be useful in the forums if you have a problem. %s', 'pagelines' ),
												   sprintf( '%s', ( pl_setting( 'enable_debug' ) ) ?
												   sprintf( '<br /><a href="%s" target="_blank">Click here</a> for your debug info.', site_url( '?pldebug=1' ) ) : '' ) )								  
							),
							array(
									'key'		=> 'disable_less_errors',
									'default'	=> false,
									'type'		=> 'check',
									'label'		=> __( 'Disable Error Notices?', 'pagelines' ),
									'title'		=> __( 'Less Notices', 'pagelines' ),
									'help'		=> __( 'Disable any error notices sent to wp-admin by the less system', 'pagelines' ),								  
							)
						)
					),
					array(
						'key'	=> 'misc_advanced_settings',
						'type'	=> 'multi',
						'col'	=> 3,
						'title'	=> __( 'Miscellaneous Config', 'pagelines' ),
						'opts'	=> array(
							array(
									'key'		=> 'load_prettify_libs',
									'type'		=> 'check',
									'label'		=> __( 'Enable Code Prettify?', 'pagelines' ),
									'title'		=> __( 'Google Prettify Code', 'pagelines' ),
									'help'		=> __( "Add a class of 'prettyprint' to code or pre tags, or optionally use the [pl_codebox] shortcode. Wrap the codebox shortcode using [pl_raw] if Wordpress inserts line breaks.", 'pagelines' )
							),
							array(
									'col'		=> 2,
									'key'		=> 'partner_link',
									'type'		=> 'text',
									'label'		=> __( 'Enter Partner Link', 'pagelines' ),
									'title'		=> __( 'PageLines Affiliate/Partner Link', 'pagelines' ),
									'help'		=> __( "If you are a <a target='_blank' href='http://www.pagelines.com/partners/'>PageLines Partner</a> enter your link here and the footer link will become a partner or affiliate link.", 'pagelines' )
							),
							array(
									'col'		=> 2,
									'key'		=> 'special_body_class',
									'type'		=> 'text',
									'label'		=> __( 'Install Class', 'pagelines' ),
									'title'		=> __( 'Current Install Class', 'pagelines' ),
									'help'		=> __( "Use this option to add a class to the &gt;body&lt; element of the website. This can be useful when using the same child theme on several installations or sub domains and can be used to control CSS customizations.", 'pagelines' )
							),

							array(
								'key'		=> 'alternative_css',
								'default'	=> false,
								'type'		=> 'check',
								'col'		=> 1,
								'label'		=> __( 'Enable Alternative CSS URLS', 'pagelines' ),
								'help'		=> __( 'Some hosts with aggressive caches have issues with the CSS files, this is a possible workaround.', 'pagelines' )				
							)
						)
					),
					
				)
			),
		);

		return $d;
	}
	
	function dms_intro(){

		?>
		<p><?php _e( 'Editing with DMS is done completely on the front end of your site. This allows you to customize in a way that feels more direct and intuitive than when using the admin.', 'pagelines' ); ?></p>
		<p><?php _e( 'Just visit the front end of your site (as an admin) and get started!', 'pagelines' ); ?>
		</p>
		<p><a class="button button-primary" href="<?php echo site_url(); ?>"><?php _e( 'Edit Site Using DMS', 'pagelines' ); ?>
		</a></p>
		
		<?php 
		
	}
		
	function dms_tools_less(){

		?>
		<form id="pl-dms-less-form" class="dms-update-setting" data-setting="custom_less">		
			<textarea id="pl-dms-less" name="pl-dms-less" class="html-textarea code_textarea input_custom_less large-text" data-mode="less"><?php echo pl_setting('custom_less');?></textarea>
			<p><input class="button button-primary" type="submit" value="<?php _e( 'Save LESS', 'pagelines' ); ?>
			" /><span class="saving-confirm"></span></p>
		</form>		
		<?php 
		
	}
	
	function dms_scripts_template(){
		?>

			<form id="pl-dms-scripts-form" class="dms-update-setting" data-setting="custom_scripts">
				<textarea id="pl-dms-scripts" name="pl-dms-scripts" class="html-textarea code_textarea input_custom_scripts large-text" data-mode="htmlmixed"><?php echo stripslashes( pl_setting( 'custom_scripts' ) );?></textarea>
				<p><input class="button button-primary" type="submit" value="<?php _e( 'Save Scripts', 'pagelines' ); ?>
				" /><span class="saving-confirm"></span></p>
			</form>
		<?php
	}
	
	function dms_debug() {
		?>
		<form id="pl-dms-debug-form" class="dms-update-setting" data-setting="enable_debug" data-type="check">
			
			<input type="checkbox" name="enable_debug" class="input_enable_debug" <?php checked( pl_setting( 'enable_debug' ), 1 ); ?> />
			<input class="button button-primary" type="submit" value="<?php _e( 'Update', 'pagelines' ); ?>
			" /><span class="saving-confirm"></span>
		</form>
		<?php
	}
}