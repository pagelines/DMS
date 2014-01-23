<?php
/*
	Section: Canvas Area
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates a full width area with a nested content width region for placing sections and columns.
	Class Name: PLSectionArea
	Filter: full-width
	Loading: active
*/


class PLSectionArea extends PageLinesSection {

	
	function section_opts(){

		$options = array();

		$options[] = array(

			'key'			=> 'pl_area_pad_selects',
			'type' 			=> 'multi',
			'label' 	=> __( 'Set Area Padding', 'pagelines' ),
			'opts'	=> array(
				array(
					'key'			=> 'pl_area_pad',
					'type' 			=> 'count_select_same',
					'count_start'	=> 0,
					'count_number'	=> 200,
					'count_mult'	=> 10,
					'suffix'		=> 'px',
					'label' 	=> __( 'Area Padding (px)', 'pagelines' ),
				),
				array(
					'key'			=> 'pl_area_pad_bottom',
					'type' 			=> 'count_select_same',
					'count_start'	=> 0,
					'count_number'	=> 200,
					'count_mult'	=> 10,
					'suffix'		=> 'px',
					'label' 	=> __( 'Area Padding Bottom (if different)', 'pagelines' ),
				),
				array(
					'key'			=> 'pl_area_height',
					'type' 			=> 'text',
					'label' 	=> __( 'Area Minimum Height (px)', 'pagelines' ),
				),
			),
			

		);
		
		$options[] = array(

			'key'			=> 'pl_area_styling',
			'type' 			=> 'multi',
			'col'			=> 3,
			'label' 	=> __( 'Area Styling', 'pagelines' ),
			'opts'	=> array(
				array(
					'key'			=> 'pl_area_parallax',
					'type' 			=> 'select',
					'opts'			=> array(
						''						=> array('name' => "No Scroll Effect"),
						'pl-parallax'			=> array('name' => "Parallaxed Background Image"),
						'pl-scroll-translate'	=> array('name' => "Translate Content on Scroll"),
					),
					'label' 	=> __( 'Scrolling effects and parallax.', 'pagelines' ),
				),
				array(

					'key'			=> 'pl_area_bg',
					'type' 			=> 'select_theme',
					
					'label' 	=> __( 'Area Theme', 'pagelines' ),

				),
				
				array(
					'key'			=> 'pl_area_bg_color_enable',
					'type' 			=> 'check',
					'label' 	=> __( 'Use Custom Background Color?', 'pagelines' ),
				),
				array(
					'key'			=> 'pl_area_bg_color',
					'type' 			=> 'color',
					'label' 	=> __( 'Select Custom Background Color', 'pagelines' ),
				)
			),
			'help' => __( 'Use a combination of the area color theme (which sets text color and base background color) along with images or a custom background color to create completely custom effects.', 'pagelines' ),

		);
		
		$options[] = array(

			'key'			=> 'pl_area_bg',
			'col'			=> 2,
			'type' 			=> 'multi',
			'label' 	=> __( 'Area Background', 'pagelines' ),
			'opts'	=> array(
				array(

					'key'			=> 'pl_area_image',
					'type' 			=> 'image_upload',
					'sizelimit'		=> 800000,
					'label' 	=> __( 'Background Image', 'pagelines' ),
				),
				array(
					'key'			=> 'pl_area_bg_repeat',
					'type' 			=> 'check',
					'label' 	=> __( 'Repeat Background Image', 'pagelines' ),
				),
				array(

					'key'			=> 'pl_area_video',
					'type' 			=> 'media_select_video',
					'label' 	=> __( 'Video Background', 'pagelines' ),
				),
				
			),
			

		);
		
		return $options;
	}
	
	function before_section_template( $location = '' ) {

		$scroll_effect = $this->opt('pl_area_parallax');
		
		if( $scroll_effect && $scroll_effect == 1 ){
			$scroll_effect = 'pl-parallax';
		}
		

		$this->wrapper_classes['background'] = $this->opt('pl_area_bg');
		$this->wrapper_classes['scroll'] = $scroll_effect;
		
		if( $this->opt('pl_area_video') )
			$this->wrapper_classes['special'] = 'bg-video-canvas';

	}

	

	function section_template( ) {
		
		$section_output = (!$this->active_loading) ? render_nested_sections( $this->meta['content'], 1) : '';
		
		$style = '';
		$inner_style = '';
		
		$inner_style .= ($this->opt('pl_area_height')) ? sprintf('min-height: %spx;', $this->opt('pl_area_height')) : '';
		
		$style .= ($this->opt('pl_area_image')) ? sprintf('background-image: url(%s);', $this->opt('pl_area_image')) : '';
		
		$classes = '';
		
		$style .= ( $this->opt('pl_area_bg_color_enable') && $this->opt('pl_area_bg_color') ) ? sprintf( 'background: %s;', pl_sanitize_color( $this->opt('pl_area_bg_color') ) ) : '';	
		
		$classes .= ($this->opt('pl_area_bg_repeat')) ? ' pl-bg-repeat' : ' pl-bg-cover';
		
		// If there is no output, there should be no padding or else the empty area will have height.
		if ( $section_output ) {
			
			// global
			$default_padding = pl_setting('section_area_default_pad', array('default' => '20'));
			// opt	
			$padding		= rtrim( $this->opt('pl_area_pad',			array( 'default' => $default_padding ) ), 'px' ); 			
			$padding_bottom	= rtrim( $this->opt('pl_area_pad_bottom',	array( 'default' => $padding ) ), 'px' ); 
			
			$style .= sprintf('padding-top: %spx; padding-bottom: %spx;',
				$padding,
				$padding_bottom
			);
			
			$content_class = $padding ? 'nested-section-area' : '';
			$buffer = pl_draft_mode() ? sprintf('<div class="pl-sortable pl-sortable-buffer span12 offset0"></div>') : '';
			$section_output = $buffer . $section_output . $buffer;
		}
		else {
			$pad_css = ''; 
			$content_class = '';
		}
		
		$video = '';
		if( $this->opt('pl_area_video') ){
			
			$videos = pl_get_video_sources( array( $this->opt('pl_area_video'), $this->opt('pl_area_video_2') ) );
			$video = sprintf(
				'<div class="bg-video-contain"><video poster="%s" class="bg-video" autoplay loop>%s</video></div>', 
				$this->opt('pl_area_image'), 
				$videos
			);

		}
		
	?>
	<div class="pl-area-wrap <?php echo $classes;?>" style="<?php echo $style;?>">
		<?php echo $video; ?>
		<div class="pl-content <?php echo $content_class;?>">
			<div class="pl-inner area-region pl-sortable-area editor-row" style="<?php echo $inner_style;?>">
				<?php  echo $section_output; ?>
			</div>
		</div>
	</div>
	<?php
	}


}
