<?php



/*
 * STANDARD POST HELPERS
 */ 

function pagelines_media( $args = array() ){
	
	global $post;
	
	$defaults = array(
		
		'thumb-size'	=> 'landscape-thumb',
		'id'			=> $post->ID,
		
	); 
	
	$args = wp_parse_args( $args, $defaults );

	$vars = array(
		'embed'			=> get_post_meta( $args['id'], '_pagelines_video_embed', true),
		'm4v'			=> get_post_meta( $args['id'], '_pagelines_video_m4v', true),
		'ogv'			=> get_post_meta( $args['id'], '_pagelines_video_ogv', true),
		'poster'		=> get_post_meta( $args['id'], '_pagelines_video_poster', true),
		'gallery'		=> get_post_meta( $args['id'], '_pagelines_gallery_slider', true),
	);
	
	$args = wp_parse_args( $args, $vars );

	$post_format = get_post_format();
	
	
	// VIDEO
	if( $post_format == 'video' && ( ! empty( $args['embed'] ) || ! empty( $args['m4v'] ) || ! empty( $args['mov'] ) ) ){
		
	    if( !empty( $args['embed'] ) ) {
			
			$media = sprintf( '<div class="video">%s</div>', do_shortcode( $args['vid_embed'] ) );	

		} else {

			$media = sprintf( '<div class="video">[video mp4="%s" ogv="%s"  poster="%s"]</div>', $args['m4v'], $args['ogv'], $args['poster']);	

		} 
	} 
	
	// GALLERY
	else if( $post_format == 'gallery' && !empty( $args['gallery'] ) ){
		
	    $gallery_ids = pl_get_attachment_ids_from_gallery();
		
		ob_start();
	 	?>

		<div class="flex-gallery"> 
			<ul class="slides">
			<?php 
				foreach( $gallery_ids as $image_id ) {
					
					$attachment = get_post( $image_id );
					
					$image = wp_get_attachment_image( $image_id, '', false  );
					
					$caption = ( $attachment->post_excerpt != '' ) ? sprintf('<p class="flex-caption">%s</p>', $attachment->post_excerpt) : '';
					
					printf( '<li>%s %s</li>', $image, $caption);
				} ?>
			</ul>
		</div><!--/gallery-->
		<?php 
		
		$media = ob_get_clean();
	}
	
	// STANDARD THUMB
	elseif ( has_post_thumbnail() ) {
		
		 $media = sprintf('<a class="post-thumbnail-link" href="%s">%s</a>', get_permalink(), get_the_post_thumbnail( $args['id'], $args['thumb-size'], array('title' => ''))); 
		
	} else 
		$media = '';
	
	return do_shortcode( $media );
}

function pl_get_attachment_ids_from_gallery() {
	
	global $post;

	if($post != null) {

	$attachment_ids = array();  
	$pattern = get_shortcode_regex();
	$ids = array();

		if (preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches ) ) {   //finds the "gallery" shortcode and puts the image ids in an associative array at $matches[3]
		
			$count = count( $matches[3] );      //in case there is more than one gallery in the post.
		
			for ($i = 0; $i < $count; $i++){
			
				$atts = shortcode_parse_atts( $matches[3][$i] );
				if ( isset( $atts['ids'] ) ){
					$attachment_ids = explode( ',', $atts['ids'] );
					$ids = array_merge($ids, $attachment_ids);
				}
			
			}	
		}

		return $ids;
	
	} else {
		
		$ids = array();
		
		return $ids;
		
	}


}

	