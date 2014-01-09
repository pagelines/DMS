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
					the_title( '<h2 class="entry-title post-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
				endif;
			?>

		</header><!-- .entry-header -->
		<div class="entry-content">
			<?php
				the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'pagelines' ) );
				wp_link_pages( array(
					'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'pagelines' ) . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
				) );
			?>
		</div><!-- .entry-content -->
	</article><!-- #post-## -->
	<?php
	
	
endwhile;
