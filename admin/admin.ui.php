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
			
				
					
				
				$desc = ( isset($groups['desc']) && ! empty($groups['desc']) ) ? sprintf('<br/><small>%s</small>', $groups['desc']) : '';
				
				printf('<h3 class="pl-opt-group-header">%s %s</h3>', $groups['title'], $desc); 
				
				echo '<table class="form-table fix"><tbody>';
			
				foreach( $groups['opts'] as $o ){
				
					$option_engine->option_engine( $o );
				
				}
			
				
				echo '</tbody></table>';
			
			}
			
			if( ! isset( $this->current_tab_config['hide_save'] ) || empty( $this->current_tab_config['hide_save'] ) )
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
	
	function set_option_up( $o ){
		
		$o = wp_parse_args( $o, $this->defaults );
		
		$o['name'] = sprintf( 'settings[%s]', $o['key'] );
		
		$o['id'] = $o['key'];
		
		$o['val'] = pl_setting( $o['key'] );
		
		return $o;
		
	}

	/**
	 * Option generation engine
	 */
	function option_engine( $o ){
		
		$o = $this->set_option_up( $o );

		
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
			$opt = $this->set_option_up( $opt );
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
		<p><input class="pl-opt pl-colorpicker" type="text" name="<?php echo $o['name'];?>" placeholder="" value="<?php echo pl_hashify( $o['val'] );?>"/> <span class="description"><?php echo $o['label'];?></span></p>
	
		<?php
	}
	
	function option_check( $o ){
		
		$val = ( !empty( $o['val'] ) ) ? 'checked' : '';
		?>
		
		<p><label for="<?php echo $o['id'];?>" class="image_uploader"><input type="hidden" class="checkbox-toggle" name="<?php echo $o['name'];?>" value="<?php echo $o['val'];?>"><input id="<?php echo $o['id'];?>" class="pl-opt checkbox-input" type="checkbox" <?php echo $val;?> /> <span class="description"><?php echo $o['label'];?></span></label></p>
	
		<?php
	}
	
	function option_text( $o ){
		?>
		<label for="upload_image" class="image_uploader"></label>
		<p><input class="pl-opt" type="text" name="<?php echo $o['name'];?>" placeholder="" value="<?php echo $o['val'];?>" /> <span class="description"><?php echo $o['label'];?></span></p>
	
		<?php
	}
	
	function option_typography( $o ){
		
		$size_key = $o['key'] . '_size';
		$weight_key = $o['key'] . '_weight';
		
		$selects = array(
			'font'	=> array(
				'key'	=> $o['key'],
				'val'	=> $o['val'], 
				'name'	=> $o['name'],
				'id'	=> $o['id'], 
				'items'	=> array()
			), 
			'size'	=> array(
				'key'	=> $size_key,
				'val'	=> pl_setting( $size_key ), 
				'name'	=> sprintf( 'settings[%s]', $size_key ),
				'id'	=> $size_key,
				'items'	=> array()
			),
			'weight'	=> array(
				'key'	=> $weight_key,
				'val'	=> pl_setting( $weight_key ), 
				'name'	=> sprintf( 'settings[%s]', $weight_key ),
				'id'	=> $weight_key,
				'items'	=> array()
			)
		);
		

		$items = pl_get_foundry();	
		if( is_array( $items ) ){
			foreach( $items as $val => $i ){
				$selects['font']['items'][ $val ] = array( 
					'name' 	=> $i['name'], 
					'val'	=> ( $selects['font']['val'] == $val ) ? 'selected' : ''
				);
			}
		}
		
		$sizes = array();
		for( $i = 10; $i <= 30; $i++){
			$selects['size']['items'][ $i ] = array(
				'name' => $i.'px',
				'val'	=> ( $selects['size']['val'] == $i ) ? 'selected' : ''
			);
		}
		
		$items = array(
			'300' => '300 - Light',
			'400' => '400 - Semi Light',
			'500' => '500 - Normal',
			'600' => '600 - Semi Bold',
			'800' => '800 - Bold'
		);
		
		if( is_array( $items ) ){
			foreach( $items as $val => $i ){
				$selects['weight']['items'][ $val ] = array( 
					'name' => $i,
					'val'	=> ( $selects['weight']['val'] == $i ) ? 'selected' : ''
				);
			}
		}
		?>
	
		<p>
			
			<?php foreach( $selects as $type => $info): ?>
				<div class="select-contain">
					<label class="label-small" for="<?php echo $info['id'];?>"><?php echo ucfirst($type); ?></label>
					<select id="<?php echo $info['id'];?>" class="pl-opt chosen-select" type="select" name="<?php echo $info['name'];?>"  >
						<option value="">Default</option>
						<?php foreach( $info['items'] as $key => $s )
									printf('<option value="%s" %s>%s</option>', $key, $s['val'], $s['name']); 
						?>
					</select>
				</div>
			<?php endforeach; ?>
		
		</p>
	
		<?php
	}
	
	function option_select( $o, $type = '' ){
		
		$select_opts = array();
		
		if( $type == 'menu' ){
			$items = wp_get_nav_menus( array( 'orderby' => 'name' ) );
		
			if( is_array( $items ) ){
				foreach( $items as $m ){
					$select_opts[ $m->term_id ] = array( 'name' => $m->name );
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
		
		foreach( $select_opts as $v => $s ){
			
			$select_opts[ $v ][ 'val' ] = ( $o['val'] == $v ) ? 'selected' : '';
			
		}
	
		?>
	
		<p>
			<select class="pl-opt chosen-select" type="select" name="" placeholder="" >
				<option value="">Default</option>
				<?php foreach( $select_opts as $key => $s )
							printf( '<option value="%s" %s>%s</option>', $key, $s['val'], $s['name'] ); 
				?>
			</select> 
			<span class="description"><?php echo $o['label'];?></span>
		</p>
	
		<?php
	}
	
	function option_image_upload( $o ){
		
		$val = ( !empty( $o['val'] ) ) ? $o['val'] : PL_IMAGES . '/image-preview.jpg';
		?>
		<label for="upload_image" class="image_uploader">
			<div class="image_preview">
				<div class="image_preview_wrap">
					<img class="the_preview_image" src="<?php echo $val;?>" />
				</div>
			</div>
			<div class="image_input">
		    	<p><input class="upload_image_option pl-opt" type="text" size="36" name="<?php echo $o['name'];?>" placeholder="Enter URL or Upload Image" value="<?php echo $o['val'];?>" /> <span class="description"><?php echo $o['label'];?></span></p>
		    	<p><button class="button button-primary image_upload_button"><i class="pl-di pl-di-upload"></i> Upload Image</button></p>
		    	
			</div>
			<div class="clear"></div>
		</label>
		
		<?php
	}
	
	


} // End of Class