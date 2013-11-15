<?php


class EditorAdmin {
	
	function __construct(){
		
		add_action( 'pagelines_options_dms_less', array( $this, 'dms_tools_less') );
		add_action( 'pagelines_options_dms_scripts', array( $this, 'dms_scripts_template') );
		add_action( 'pagelines_options_dms_intro', array( $this, 'dms_intro') );
		
	}
	
	function admin_array(){

		$d = array(
			'tabs'	=> array(
				'title'		=> __( 'PageLines DMS Settings', 'pagelines' ),
				'slug'		=> 'dms_settings',
				'groups'	=> array(
					array(
						'title'	=> __( 'Editing Your Site With DMS', 'pagelines' ), 
						'opts'	=> array(
							'intro'		=> array(
								'type'		=> 'dms_intro',
								'title'		=> __( 'Welcome to DMS!', 'pagelines' ),
							),
						)
					),
					array(
						'title'	=> __( 'Your PageLines Account', 'pagelines' ), 
						'opts'	=> array(
							'intro'		=> array(
								'type'		=> 'pagelines_account',
								'title'		=> __( 'Activate Your Account', 'pagelines' ),
							),
						)
					),
					array(
						'title'	=> __( 'DMS Fallbacks', 'pagelines' ),
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
			)
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
	
	function get_account_data(){
		
		$data = array(
			'email'		=> '', 
			'key'		=> '',
			'message'	=> '', 
			'avatar'	=> '', 
			'name'		=> '',
			'description'	=> '',
			'active'	=> false, 
			'real_user'	=> false,
			'url'		=> '',
			'karma'		=> 0,
			'lifetime_karma'	=> 0
			
		);
		
		$activation_data = (get_option( 'dms_activation' ) && is_array(get_option( 'dms_activation' ))) ? get_option( 'dms_activation' ) : array();
		
		$data = wp_parse_args( $activation_data, $data);
		
		return $data;
		
	}
	
	function pagelines_account(){

		$disabled = '';
		$email = '';
		$key = '';
		$activate_text = '<i class="icon-star"></i> Activate Pro';
		$activate_btn_class = 'btn-primary'; 
		
		
		$data = $this->get_account_data();
		
		$active = $data['active'];
		
		$disable = ($active) ? 'disabled' : '';

		$activation_message = ($data['message'] == '') ? 'Site not activated.' : $data['message'];

		?>
		
		<div class="account-details alert alert-warning" style="<?php if(! $active) echo 'display: block;';?>">
			<?php if( ! $active || $active == ''):  ?>
				<strong><i class="icon-star-half-empty"></i> <?php _e( 'Site Not Activated', 'pagelines' ); ?>
				</strong>
			<?php endif; ?>
		</div>
		<?php if( $active ):  ?>
		
			<div class="account-field alert">
		
				<label for="pl_activation">
					<i class="icon-star"></i> <?php _e( 'Pro Activated!', 'pagelines' ); ?>
					 
					<small><?php printf($activation_message);  ?></small>
				</label>
				<button class="btn settings-action refresh-user btn-primary" data-action="pagelines_account"><i class="icon-refresh" ></i> <?php _e( 'Update Info', 'pagelines' ); ?>
				</button>
				<button class="btn settings-action deactivate-key" data-action="pagelines_account"><i class="icon-remove" style="color: #ff0000;"></i> <?php _e( 'Deactivate', 'pagelines' ); ?>
				</button>
		
			</div>
		
		<?php endif; ?>
		
		<div class="pl-input-field">

			<input type="text" class="pl-text-input" name="pl_email" id="pl_email" placeholder="Enter Account Email" value="<?php echo $data['email']; ?>" <?php echo $disable; ?> /> &nbsp; <span class="description"><?php _e( "Your PageLines account email."); ?></span>
			
		</div>
	
		<div class="pl-input-field">	 
			<input type="password" class="pl-text-input" name="pl_activation" id="pl_activation" placeholder="<?php _e( 'Enter Pro Key', 'pagelines' ); ?>" value="<?php echo $data['key']; ?>" <?php echo $disable; ?> /> &nbsp; <span class="description"><?php _e( "PageLines Updates and Support Activation Key."); ?></span>
		
		</div>
		
		<?php if( ! $active ): ?>
			<div class="pl-input-field">
				<div class="submit-area account-field">
					<button class="button button-primary settings-action" data-action="pagelines_account">
					<?php _e( 'Update Account', 'pagelines' ); ?>
					 <i class="icon-chevron-sign-right"></i></button>
			
				</div>
			</div>
		<?php endif; 
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
}