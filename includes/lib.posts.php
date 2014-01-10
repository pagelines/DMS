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
	
	// STANDARD THUMB
	elseif ( has_post_thumbnail() ) {
		
		 $media = sprintf('<a class="post-thumbnail-link" href="%s">%s</a>', get_permalink(), get_the_post_thumbnail( $args['id'], $args['thumb-size'], array('title' => ''))); 
		
	} else 
		$media = '';
	
	return do_shortcode( $media );
}