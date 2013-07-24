<?php
/*
	Section: Swiper
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: An advanced, touch and swipe enabled image and rich media slider.
	Class Name: PLSwiper
	Filter: slider, gallery
	Edition: pro
*/


class PLSwiper extends PageLinesSection {

	var $default_limit = 3;

	function section_styles(){
		wp_enqueue_script('royalslider', $this->base_url.'/royalslider/jquery.royalslider.min.js', array('jquery'));
		wp_enqueue_style( 'royalslider-css', $this->base_url.'/royalslider/royalslider.css');
		wp_enqueue_style( 'royalslider-theme', $this->base_url.'/royalslider/skins/default/rs-default.css');
		
	}
	
	function section_head(){
		?>
		
		 <script>
		      jQuery(document).ready(function(jQuery) {
		 		jQuery('#swiper-gallery').royalSlider({
			
					autoScaleSlider: true,
					autoHeight: true,
					arrowsNav: false,
					fadeinLoadedSlide: true,
					controlNavigation: 'thumbnails',
					
					thumbs: {
						autoCenter: true,
						fitInViewport: false,
						orientation: 'horizontal',
						spacing: 10,
						paddingBottom: 0
					},
					keyboardNavEnabled: true,
					imageScaleMode: 'fill',
					imageAlignCenter:true,
					slidesSpacing: 0,
					loop: false,
					loopRewind: true,
					numImagesToPreload: 3,
					video: {
					 autoHideArrows:true,
					 autoHideControlNav:false,
					 autoHideBlocks: true
					}, 
					
					autoHeight: true,
					controlNavigation: 'thumbnails',
				
					})
		})

		    </script>
		
		
		<?php
	}

	function section_opts(){
		$options = array();

		$options[] = array(

			'title' => __( 'Swiper Configuration', 'pagelines' ),
			'type'	=> 'multi',
			'opts'	=> array(
				array(
					'key'			=> 'swiper_count',
					'type' 			=> 'count_select',
					'count_start'	=> 1,
					'count_number'	=> 12,
					'default'		=> 4,
					'label' 	=> __( 'Number of Slides to Configure', 'pagelines' ),
				),
				array(
					'key'			=> 'swiper_title',
					'type' 			=> 'text',
					'label' 	=> __( 'Swiper Title', 'pagelines' ),
				),
				array(
					'key'			=> 'swiper_desc',
					'type' 			=> 'textarea',
					'label' 	=> __( 'Swiper Description', 'pagelines' ),
				),
			)

		);

		$slides = ($this->opt('swiper_count')) ? $this->opt('swiper_count') : $this->default_limit;
	
		for($i = 1; $i <= $slides; $i++){

			$opts = array();

			$opts[] = array(
				'key'		=> 'swiper_title_'.$i,
				'label'		=> __( 'Slide Title', 'pagelines' ),
				'type'		=> 'text'
			);
			
			$opts[] = array(
				'key'		=> 'swiper_image_'.$i,
				'label'		=> __( 'Slide Image', 'pagelines' ),
				'type'		=> 'image_upload',
			);

			$opts[] = array(
				'key'		=> 'swiper_url_'.$i,
				'label'	=> __( 'Slide Video URL (Youtube or Vimeo)', 'pagelines' ),
				'type'	=> 'text', 
				'help'	=> __('<strong>Note:</strong> Use the regular video url, not the embed code.', 'pagelines')
			);

			$options[] = array(
				'title' 	=> __( 'Swiper Slide ', 'pagelines' ) . $i,
				'type' 		=> 'multi',
				'opts' 		=> $opts,

			);

		}

		return $options;
	}
	
	function the_media(){
		
		$num = ($this->opt('swiper_count')) ? $this->opt('swiper_count') : $this->default_limit;
		$out = array();
		
		for($i = 1; $i <= $num; $i++):
			
			$title = ($this->opt('swiper_title_'.$i)) ? $this->opt('swiper_title_'.$i) : ''; 
			$url = ($this->opt('swiper_url_'.$i)) ? $this->opt('swiper_url_'.$i) : '';
			$img = ($this->opt('swiper_image_'.$i)) ? $this->opt('swiper_image_'.$i) : '';
			
			if($url != '' || $img != ''){
				$out[] = array(
					'title'	=> $title, 
					'url'	=> $url, 
					'img'	=> $img
				);
			}
			 
			
		endfor;
		
		if( empty($out) ){
			$out[] = array(
				'title'	=> 'Introducing DMS', 
				'url'	=> 'https://www.youtube.com/watch?v=CL1jPb0_Auc', 
				'img'	=>	$this->base_url.'/leaf-img.jpg'
			);
			
			$out[] = array(
					'title'	=> 'DMS Walk Through', 
					'url'	=> 'https://www.youtube.com/watch?v=rNYiWNLtk5A', 
					'img'	=>	$this->base_url.'/dms-img.jpg'
			);
		}
		
		return $out;
	}
	
	function pl_desc(){
		?>
			<span class="pl-special-font">Watch the videos <img src="<?php echo CHILD_URL.'/img/watch-the-video-arrow.png';?>" /></span>
			<span class="pl-sep">-and-</span>
			<a href="#" class="btn btn-primary">Try the demo <i class="icon-chevron-sign-right"></i></a>
			
		<?php
	}


   function section_template( ) {
	
		$title = ( $this->opt('swiper_title') ) ? sprintf( '<h2 class="swiper-title">%s</h2>', $this->opt('swiper_title') ) : '';
		$desc = ( $this->opt('swiper_desc') ) ? sprintf( '<div class="swiper-desc">%s</div>', $this->opt('swiper_desc') ) : '';
	
		
	 ?>

	<div class="swiper-wrap">
		<?php printf('<div class="swiper-details">%s%s</div>', $title, $desc); ?>
		<div id="swiper-gallery" class="swiper-gallery royalSlider videoGallery rsDefault">
			<?php foreach($this->the_media() as $m): 
				
				$vid_url = (isset( $m['url'] ) &&  $m['url'] != '') ? sprintf('data-rsVideo="%s"', $m['url']) : ''; ?>

				<div class="swiper-slide">
					<img class="rsImg" <?php echo $vid_url;?> src="<?php echo $m['img'];?>"/>
					<span class="rsTmb">
						<div class="video-nav-img">
							<img class="theRsImg" src="<?php echo $m['img'];?>" /> 
						</div>
						<?php echo $m['title'];?>
					</span>
				</div>
			<?php endforeach; ?>
		
		</div>
	</div>

<?php }


}