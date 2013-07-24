<?php
/*
	Section: PopThumbs
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Adds columnized thumbnails that lightbox to full size images on click.
	Class Name: PLPopThumbs
	Edition: pro
	Filter: gallery
	Loading: active
*/


class PLPopThumbs extends PageLinesSection {

	var $default_limit = 4;

	function section_styles(){
		wp_enqueue_script('prettyphoto', $this->base_url.'/prettyphoto.js', array('jquery'));
		wp_enqueue_style( 'prettyphoto-css', $this->base_url.'/prettyPhoto/css/prettyPhoto.css');
		
	}
	
	function section_foot(){
		?>
		
		<script>
		  jQuery(document).ready(function(){
		    jQuery("a[rel^='prettyPhoto']").prettyPhoto();
		  });
		</script>
		
		<?php
	}
	

	function section_opts(){
		$options = array();

		$options[] = array(

			'title' => __( 'PopThumb Configuration', 'pagelines' ),
			'type'	=> 'multi',
			'opts'	=> array(
				array(
					'key'			=> 'popthumb_count',
					'type' 			=> 'count_select',
					'count_start'	=> 1,
					'count_number'	=> 12,
					'default'		=> 4,
					'label' 	=> __( 'Number of PopThumbs to Configure', 'pagelines' ),
				),
				array(
					'key'			=> 'popthumb_cols',
					'type' 			=> 'count_select',
					'count_start'	=> 1,
					'count_number'	=> 12,
					'default'		=> '3',
					'label' 	=> __( 'Number of Columns for Each Thumb (12 Col Grid)', 'pagelines' ),
				),
			)

		);

		$slides = ($this->opt('popthumb_count')) ? $this->opt('popthumb_count') : $this->default_limit;
	
		for($i = 1; $i <= $slides; $i++){

			$opts = array(

				array(
					'key'		=> 'popthumb_title_'.$i,
					'label'		=> __( 'PopThumb Title', 'pagelines' ),
					'type'		=> 'text'
				),
				array(
					'key'		=> 'popthumb_text_'.$i,
					'label'	=> __( 'PopThumb Text', 'pagelines' ),
					'type'	=> 'textarea'
				),
			);

			$opts[] = array(
				'key'			=> 'popthumb_image_'.$i,
				'label'			=> __( 'PopThumb Image', 'pagelines' ),
				'type'			=> 'image_upload',
				'sizelimit'		=> 800000,
			);
			
			$opts[] = array(
				'key'			=> 'popthumb_thumb_'.$i,
				'label'			=> __( 'PopThumb Thumb', 'pagelines' ),
				'type'			=> 'image_upload',
			);


			$options[] = array(
				'title' 	=> __( 'PopThumb ', 'pagelines' ) . $i,
				'type' 		=> 'multi',
				'opts' 		=> $opts,

			);

		}

		return $options;
	}


   function section_template( ) { 
	
		$cols = ($this->opt('popthumb_cols')) ? $this->opt('popthumb_cols') : 3;
		$num = ($this->opt('popthumb_count')) ? $this->opt('popthumb_count') : $this->default_limit;
		$width = 0;
		$output = '';
	
		for($i = 1; $i <= $num; $i++):

			$link = '';
			$title = ($this->opt('popthumb_title_'.$i)) ? $this->opt('popthumb_title_'.$i) : 'PopThumb '.$i; 
			$text = ($this->opt('popthumb_text_'.$i)) ? $this->opt('popthumb_text_'.$i) : ''; 
			$img = '';
			$thumb = ($this->opt('popthumb_title_'.$i));
			
			$attach_id = $this->opt('popthumb_image_'.$i.'_attach_id');
		
			
			if($this->opt('popthumb_image_'.$i)) {
				
				$full_img = $this->opt('popthumb_image_'.$i); 
				
			} else 
				$full_img = pl_default_image();
				
				
				
			if($this->opt('popthumb_thumb_'.$i)){
				
				$thumb_url = $this->opt('popthumb_thumb_'.$i);
				
			} elseif($attach_id && $attach_id != ''){
				
				$img = wp_get_attachment_image_src( $attach_id, 'basic-thumb'); 
			
				$thumb_url = $img[0]; 
				
			} elseif($this->opt('popthumb_image_'.$i)){
				
				$thumb_url = $this->opt('popthumb_image_'.$i);
				
			} else
				$thumb_url = pl_default_thumb();
			
			$thumb = sprintf('<img src="%s" />', $thumb_url);
			

			if($width == 0)
				$output .= '<div class="row fix">';

			$output .= sprintf(
				'<div class="span%s fix">
					<a class="popthumb" href="%s" rel="prettyPhoto[%s]">
						<span class="popthumb-thumb pl-animation pl-appear pl-contrast">
							%s
							
						</span>
						<span class="expander"><i class="icon-plus"></i></span>
					</a>
					<div class="popthumb-text">
						<h4 data-sync="popthumb_title_%s">%s</h4>
						<div class="popthumb-desc" data-sync="popthumb_text_%s">
							%s
						</div>
					</div>
				</div>',
				$cols,
				$full_img,
				$this->meta['unique'], 
				$thumb,
				$i,
				$title,
				$i,
				$text
			);

			$width += $cols;

			if($width >= 12 || $i == $num){
				$width = 0;
				$output .= '</div>';
			}


		 endfor;
	
	
	?>
	
	<div class="popthumbs-wrap pl-animation-group">
		<?php echo $output; ?>
	</div>

<?php }


}