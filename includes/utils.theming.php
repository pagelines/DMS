<?php

// This file contains utilities for theme development and theme user experience




// Blog Sections & Post Utilities


function pl_post_avatar( $post_id, $size ){
	
	$author_name = get_the_author();
	$default_avatar = PL_IMAGES . '/avatar_default.gif';
	$author_desc = custom_trim_excerpt( get_the_author_meta('description', $p->post_author), 10);
	$author_email = get_the_author_meta('email', $p->post_author);
	$avatar = get_avatar( $author_email, '32' );
	
}

function pl_list_pages( $number = 6 ){

	$pages_out = '';

	$pages = wp_list_pages('echo=0&title_li=&sort_column=menu_order&depth=1');

	$pages_arr = explode("\n", $pages);
	
	for($i=0; $i < $number; $i++){

		if(isset($pages_arr[$i]))
			$pages_out .= $pages_arr[$i];

	}
	
	return $pages_out;
	
}

function pl_recent_posts( $number = 3 ){?>
	<ul class="media-list">
		<?php

		foreach( get_posts( array('numberposts' => $number ) ) as $p ){
			
			
			$img_src = (has_post_thumbnail( $p->ID )) ? pl_the_thumbnail_url( $p->ID, 'thumbnail') : pl_default_thumb();
		
			$img = sprintf('<div class="img"><a class="the-media" href="%s" style="background-image: url(%s)"></a></div>', get_permalink( $p->ID ), $img_src);

			printf(
				'<li class="media fix">%s<div class="bd"><a class="title" href="%s">%s</a><span class="excerpt">%s</span></div></li>',
				$img,
				get_permalink( $p->ID ),
				$p->post_title,
				pl_short_excerpt($p->ID)
			);

		} ?>
	</ul>
<?php }

function pl_popular_taxonomy( $number_of_categories = 6, $taxonomy = 'category' ){
	
	$args = array( 
		'number' 	=> $number_of_categories,
		'depth' 	=> 1, 
		'title_li' 	=> '', 
		'orderby' 	=> 'count', 
		'show_count' => 1, 
		'order' 	=> 'DESC',
		'taxonomy'	=> $taxonomy,
		'echo'		=> 0
	);
	
	return wp_list_categories( $args );

}

function pl_media_list( $title, $list ){
	
	return sprintf( '<ul class="media-list"><lh class="title">%s</lh>%s</ul>', $title, $list);
	
	
}


