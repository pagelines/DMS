<?php


class EditorAdmin {
	
	function __construct(){
		
		add_action( 'pagelines_options_dms_less', array(&$this, 'dms_tools_less') );
		add_action( 'pagelines_options_dms_scripts', array(&$this, 'dms_scripts_template') );
		add_action( 'pagelines_options_dms_intro', array(&$this, 'dms_intro') );
		
	}
	
	function admin_array(){

		$d = array(
			'tabs'	=> array(
				'title'		=> 'PageLines DMS Settings',
				'slug'		=> 'dms_settings',
				'groups'	=> array(
					array(
						'title'	=> 'Editing Your Site With DMS', 
						'opts'	=> array(
							'intro'		=> array(
								'type'		=> 'dms_intro',
								'title'		=> __( 'Welcome to DMS!', 'pagelines' ),
							),
						)
					),
					array(
						'title'	=> 'DMS Fallbacks',
						'desc'	=> 'Below are secondary fallbacks to the DMS code editors. You may need these if you create errors or issues on the front end.',
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
		<p>Editing with DMS is done completely on the front end of your site. This allows you to customize in a way that feels more direct and intuitive than when using the admin. </p>
		<p>Just visit the front end of your site (as an admin) and get started!</p>
		<p><a class="button button-primary" href="<?php echo site_url(); ?>">Edit Site Using DMS</a></p>
		
		<?php 
		
	}
	
	function dms_tools_less(){

		?>
		

		<form id="pl-dms-less-form" class="dms-update-setting" data-setting="custom_less">
		
			<textarea id="pl-dms-less" name="pl-dms-less" class="html-textarea code_textarea input_custom_less large-text" data-mode="less"><?php echo pl_setting('custom_less');?></textarea>
			<p><input class="button button-primary" type="submit" value="Save LESS" /><span class="saving-confirm"></span></p>
		</form>
		
		<?php 
		
	}
	
	function dms_scripts_template(){
		?>

			<form id="pl-dms-scripts-form" class="dms-update-setting" data-setting="custom_scripts">
				<textarea id="pl-dms-scripts" name="pl-dms-scripts" class="html-textarea code_textarea input_custom_scripts large-text" data-mode="htmlmixed"><?php echo stripslashes( pl_setting( 'custom_scripts' ) );?></textarea>
				<p><input class="button button-primary" type="submit" value="Save Scripts" /><span class="saving-confirm"></span></p>
			</form>

		<?php
	}

}