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
			printf('<div id="%s" class="tabinfo">', $this->current_tab_slug );
		
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
			
			case 'select':
				$this->option_select( $o );
			break;
			
			case 'select_menu':
				$this->option_select( $o, 'menu' );
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
	
	function option_text( $o ){
		?>
		<label for="upload_image" class="image_uploader"></label>
		<p><input class="pl-opt" type="text" name="" placeholder="" /> <span class="description"><?php echo $o['label'];?></span></p>
	
		<?php
	}
	
	function option_select( $o, $type = '' ){
		?>
		<label for="upload_image" class="image_uploader"></label>
		<p><input class="pl-opt" type="text" name="" placeholder="" /> <span class="description"><?php echo $o['label'];?></span></p>
	
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