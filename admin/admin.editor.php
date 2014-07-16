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
			'pagelines_advanced' => array( 
				'title'		=> __( 'Advanced', 'pagelines' ),
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