<?php

if( have_posts() )
	while ( have_posts() ) : the_post(); 
	
	?>	
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<?php 

				if ( is_single() ) :
					the_title( '<h1 class="entry-title">', '</h1>' );
				else :	
					the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
				endif;
				
				
			?>
			<div class="metabar">
			</div>
		</header><!-- .entry-header -->
		<div class="entry-content">
			
			<?php
			
			if( is_single() ){
				
				the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'pagelines' ) );
				wp_link_pages( array(
					'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'pagelines' ) . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
				) );
				
			} else {
				the_excerpt();
				
				printf(
					'<a class="continue_reading_link btn btn-inverse" href="%s" title="%s %s">%s</a>',
					get_permalink(),
					__("View", 'pagelines'),
					the_title_attribute(array('echo'=> 0)),
					$this->continue_reading
				);
			}
				
			?>
		</div><!-- .entry-content -->
	</article><!-- #post-## -->
	<?php
	
	
endwhile;
