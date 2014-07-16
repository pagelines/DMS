<?php
/**
 *
 *
 *  Options Layout Class
 *
 *
 *  @package PageLines DMS
 *  @subpackage Options
 *  @since 4.0
 *
 */

class DMSOptionsUI {

/*
	Build The Layout
*/
	function __construct( $args = array() ) {

		$defaults = array(
				'title'			=> '',
				'callback'		=> false, 
				'config'	=> array()
			);

		$this->set = wp_parse_args( $args, $defaults );
			
		$this->config = (isset($this->set['callback'])) ? call_user_func( $this->set['callback'] ) : $this->set['config'];

		$this->current_tab_slug = (isset($_GET['tab'])) ? $_GET['tab'] : 'default';
		
		$this->current_tab_config = ( isset($this->config[$this->current_tab_slug]) ) ? $this->config[$this->current_tab_slug] : current( $this->config );

		// Draw the thing
		$this->build_header();
		$this->build_body();
		$this->build_footer();

	}


		/**
		 * Option Interface Header
		 */
		function build_header(){?>
			
			<div class="wrap pagelines-admin">
			
					
<?php }

		/**
		 * Option Interface Footer
		 */
		function build_footer(){?>
			<?php  // submit_button(); ?>
		
			</div>
		<?php }
		
		function get_nav(){
			?>
			<h2 class="nav-tab-wrapper">
				<?php

				$count = 1;
				foreach( $this->config as $slug => $tabs ){

					if( $slug == $this->current_tab_slug || ( $this->current_tab_slug == 'default' && $count == 1) ){
						$class = 'nav-tab-active';
					} else 
						$class = '';

			        printf( '<a class="nav-tab %s" href="?page=PageLines-Admin&tab=%s">%s</a>', $class, $slug, $tabs['title'] );
					$count++;
			    }


				?>
			</h2>
			<?php
		}

		/**
		 * Option Interface Body, including vertical tabbed nav
		 */
		function build_body(){
			$option_engine = new DMSOptEngine();

			$this->get_nav();
		
			// The tab container start....
			printf('<div id="%s" class="pl-admin-settings tabinfo">', $this->current_tab_slug );
		
			foreach( $this->current_tab_config['groups'] as $groups ){
			
				if( isset($groups['title']) && ! empty($groups['title']))
					printf('<h3 class="pl-opt-group-header">%s</h3>', $groups['title']); 
				
				if( isset($groups['desc']) && ! empty($groups['desc']))
					printf('<p>%s</p>', $groups['desc']);
				
				echo '<table class="form-table fix"><tbody>';
			
				foreach( $groups['opts'] as $o ){
				
					$option_engine->option_engine( $o );
				
				}
			
				
				echo '</tbody></table>';
			
			}
			
			printf('<div class="pl-save"><button class="pl-save-settings button button-primary">Save Changes</button></div>');
		
			echo '<div class="clear"></div></div>';
	
	}


} // End Class

/**
 * Option Engine Class
 *
 * Sorts and Draws options based on the 'option array'
 * Option array is loaded in config.option.php and through filters
 *
 */
class DMSOptEngine {

    /**
     * PHP5 Constructor
     *
     * @param   null $settings_field
     * @version 2.2 - alphabetized defaults listing; add 'docstitle' setting
     */
	function __construct() {
		

		$this->defaults = array(
			
			'placeholder'	=> '',
			'disabled'		=> false,
			'type'			=> 'text',
			'label'			=> false,
			'key'			=> '',
			'title'			=> false,
			'help'			=> ''
		);
		
	}

	/**
	 * Option generation engine
	 */
	function option_engine( $o ){
		
		$o = wp_parse_args( $o, $this->defaults );

		
		if($o['disabled'])
			return;
			
	 
		$o['placeholder'] = pl_html($o['placeholder']);
		
		?>
		
				<tr valign="top">
					<th scope="row" class="titledesc"><label for="<?php echo $o['key']; ?>"><?php echo $o['title']; ?></label></th>
					<td> 
						<?php $this->option_breaker( $o ); ?> 
						<?php if($o['help'] != ''): ?><div class="pl-help"><?php echo $o['help'];?></div><?php endif;?>
					</td>
				</tr>

<?php  }
	

	/**
	 * 
	 * Option Breaker 
	 * Switches through an option array, generating the option handling and markup
	 *
	 */
	function option_breaker( $o ){

		switch ( $o['type'] ){
			
			case 'multi':
				$this->option_multi( $o );
			break;
			
			case 'image_upload':
				$this->option_image_upload( $o );
			break;
			
			case 'check':
				$this->option_check( $o );
			break;
			
			case 'select':
				$this->option_select( $o );
			break;
			
			case 'select_menu':
				$this->option_select( $o, 'menu' );
			break;
			
			case 'select_icon':
				$this->option_select( $o, 'icon' );
			break;
			
			case 'action_button':
				$this->option_button( $o, 'action' );
			break;
			
			case 'color':
				$this->option_color( $o );
			break;
			
			case 'typography':
				$this->option_typography( $o );
			break;
			
			case 'text':
				$this->option_text( $o );
			break;

			default :
				do_action( 'pagelines_options_' . $o['type'] , $o);
				break;

		} 

	}
	
	function option_multi( $o ){
		foreach( $o['opts'] as $key => $opt ){
			$opt = wp_parse_args( $opt, $this->defaults );
			$this->option_breaker( $opt );
		}
	}
	
	function option_button( $o, $type = ''){
		?>
		
		<p><button for="upload_image" class="image_uploader button button-primary"><?php echo $o['label'];?></button></p>
	
		<?php
	}
	
	function option_color( $o, $type = ''){
		?>
		
		<p><input class="pl-opt pl-colorpicker" type="text" name="" placeholder="" /> <span class="description"><?php echo $o['label'];?></span></p>
	
		<?php
	}
	
	function option_check( $o ){
		?>
		
		<p><label for="upload_image" class="image_uploader"><input class="pl-opt" type="checkbox" name="" placeholder="" /> <span class="description"><?php echo $o['label'];?></span></label></p>
	
		<?php
	}
	
	function option_text( $o ){
		?>
		<label for="upload_image" class="image_uploader"></label>
		<p><input class="pl-opt" type="text" name="" placeholder="" /> <span class="description"><?php echo $o['label'];?></span></p>
	
		<?php
	}
	
	function option_typography( $o ){
		
		$fonts = array();
		$items = pl_get_foundry();
		
		if( is_array( $items ) ){
			foreach( $items as $val => $i ){
				$fonts[ $val ] = array( 'name' => $i['name'] );
			}
		}
		
		$sizes = array();
		for( $i = 10; $i <= 30; $i++){
			$sizes[ $i ] = array('name' => $i.'px');
		}
		
		$weights = array();
		$items = array(
			'300' => '300 - Light',
			'400' => '400 - Semi Light',
			'500' => '500 - Normal',
			'600' => '600 - Semi Bold',
			'800' => '800 - Bold'
		);
		
		if( is_array( $items ) ){
			foreach( $items as $val => $i ){
				$weights[ $val ] = array( 'name' => $i );
			}
		}
		?>
	
		<p>
			<select class="pl-opt chosen-select" type="select" name="" placeholder="" >
				<option value="">Default</option>
				<?php foreach( $fonts as $key => $s )
							printf('<option value="%s">%s</option>', $key, $s['name']); 
				?>
			</select>
			<select class="pl-opt chosen-select" type="select" name="" placeholder="" >
				<option value="">Default</option>
				<?php foreach( $sizes as $key => $s )
							printf('<option value="%s">%s</option>', $key, $s['name']); 
				?>
			</select>
			<select class="pl-opt chosen-select" type="select" name="" placeholder="" >
				<option value="">Default</option>
				<?php foreach( $weights as $key => $s )
							printf('<option value="%s">%s</option>', $key, $s['name']); 
				?>
			</select>
		</p>
		<p>
			<textarea class="pl-opt pl-typography-preview" rows="1">The quick brown fox jumps over the lazy dog.</textarea>
		</p>
	
		<?php
	}
	
	function option_select( $o, $type = '' ){
		
		$select_opts = array();
		
		if( $type == 'menu' ){
			$items = wp_get_nav_menus( array( 'orderby' => 'name' ) );
			
			if( is_array( $items ) ){
				foreach( $items as $m ){
					$select_opts[ $m->slug ] = array( 'name' => $m->name );
				}
			}
			
		} elseif( $type == 'icon' ){
		
			$items = pl_icon_array( );

			if( is_array( $items ) ){
				foreach( $items as $m ){
					$select_opts[ $m ] = array( 'name' => $m );
				}
			}
			
		} else {
			
			if( is_array( $o['opts'] ) )
				$select_opts = $o['opts'];
			
			
		}
		?>
		<label for="upload_image" class="image_uploader"></label>
		<p>
			<select class="pl-opt chosen-select" type="select" name="" placeholder="" >
				<option value="">Default</option>
				<?php foreach( $select_opts as $key => $s )
							printf('<option value="%s">%s</option>', $key, $s['name']); 
				?>
			</select> 
			<span class="description"><?php echo $o['label'];?></span>
		</p>
	
		<?php
	}
	
	function option_image_upload( $o ){
		
		$val = PL_IMAGES . '/image-preview.jpg';
		?>
		<label for="upload_image" class="image_uploader">
			<div class="image_preview">
				<div class="image_preview_wrap">
					<img src="<?php echo $val;?>" />
				</div>
			</div>
			<div class="image_input">
		    	<p><input class="upload_image_option pl-opt" type="text" size="36" name="" placeholder="Enter URL or Upload Image" /> <span class="description"><?php echo $o['label'];?></span></p>
		    	<p><button class="button button-primary image_upload_button"><i class="pl-di pl-di-upload"></i> Upload Image</button></p>
		    	
			</div>
			<div class="clear"></div>
		</label>
		
		<?php
	}
	
	


} // End of Class