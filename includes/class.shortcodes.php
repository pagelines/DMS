<?php
/**
 * PageLines_ShortCodes
 *
 * This file defines return functions to be used as shortcodes by users and developers
 *
 * @package     PageLines Framework
 * @subpackage  Sections
 * @since       2.2
 *
 */

class PageLines_ShortCodes {


	function __construct() {

		self::register_shortcodes( $this->shortcodes_core() );

		// Make widgets process shortcodes
		add_filter( 'widget_text', 'do_shortcode' );
	}

	private function shortcodes_core() {

		$core = array(

			'button'					=>	array( 'function' => 'pagelines_button_shortcode' ),
			'post_time'					=>	array( 'function' => 'pagelines_post_time_shortcode' ),

			'pl_button'					=>	array( 'function' => 'pl_button_shortcode' ),
			'pl_buttongroup'            =>  array( 'function' => 'pl_buttongroup_shortcode' ),
			'pl_buttondropdown'         =>	array( 'function' => 'pl_buttondropdown_shortcode' ),
			'pl_splitbuttondropdown'    =>  array( 'function' => 'pl_splitbuttondropdown_shortcode' ),
			'pl_tooltip'                =>  array( 'function' => 'pl_tooltip_shortcode' ),
			'pl_popover'                =>	array( 'function' => 'pl_popover_shortcode' ),
			'pl_accordion'              =>	array( 'function' => 'pl_accordion_shortcode' ),
			'pl_accordioncontent'       =>  array( 'function' => 'pl_accordioncontent_shortcode' ),
			'pl_carousel'               =>	array( 'function' => 'pl_carousel_shortcode' ),
			'pl_carouselimage'          =>	array( 'function' => 'pl_carouselimage_shortcode' ),
			'pl_tabs'                   =>	array( 'function' => 'pl_tabs_shortcode' ),
			'pl_tabtitlesection'        =>	array( 'function' => 'pl_tabtitlesection_shortcode' ),
			'pl_tabtitle'               =>	array( 'function' => 'pl_tabtitle_shortcode' ),
			'pl_tabcontentsection'      =>	array( 'function' => 'pl_tabcontentsection_shortcode' ),
			'pl_tabcontent'             =>	array( 'function' => 'pl_tabcontent_shortcode' ),
			'pl_modal'				    =>	array( 'function' => 'pl_modal_shortcode' ),
			'pl_blockquote'				=>	array( 'function' => 'pl_blockquote_shortcode' ),
			'pl_alertbox'				=>	array( 'function' => 'pl_alertbox_shortcode' ),
			'show_authors'				=>	array( 'function' => 'show_multiple_authors' ),
			'pl_codebox'			    =>	array( 'function' => 'pl_codebox_shortcode' ),
			'pl_label'				    =>	array( 'function' => 'pl_label_shortcode' ),
			'pl_badge'			        =>	array( 'function' => 'pl_badge_shortcode' ),
			'googleplus'				=>	array( 'function' => 'pl_googleplus_button' ),
			'linkedin'			    	=>	array( 'function' => 'pl_linkedinshare_button' ),
			'like_button'				=>	array( 'function' => 'pl_facebook_shortcode' ),
			'twitter_button'			=>	array( 'function' => 'pl_twitter_button' ),
			'pinterest'		        	=>	array( 'function' => 'pl_pinterest_button' ),
			'post_date'					=>	array( 'function' => 'pagelines_post_date_shortcode' ),
			'post_author'				=>	array( 'function' => 'pagelines_post_author_shortcode' ),
			'post_author_link'			=>	array( 'function' => 'pagelines_post_author_link_shortcode' ),
			'post_author_posts_link'	=>	array( 'function' => 'pagelines_post_author_posts_link_shortcode' ),
			'post_comments'				=>	array( 'function' => 'pagelines_post_comments_shortcode' ),
			'post_tags'					=>	array( 'function' => 'pagelines_post_tags_shortcode' ),
			'post_categories'			=>	array( 'function' => 'pagelines_post_categories_shortcode' ),
			'post_type'					=>	array( 'function' => 'pagelines_post_type_shortcode' ),
			'post_edit'					=>	array( 'function' => 'pagelines_post_edit_shortcode' ),
			'container'					=>	array( 'function' => 'dynamic_container' ),
			'cbox'						=>	array( 'function' => 'dynamic_box' ),
			'post_feed'					=>	array( 'function' => 'get_postfeed' ),
			'chart'						=>	array( 'function' => 'chart_shortcode' ),
			'googlemap'					=>	array( 'function' => 'googleMaps' ),
			'themeurl'					=>	array( 'function' => 'get_themeurl' ),
			'link'						=>	array( 'function' => 'create_pagelink' ),
			'bookmark'					=>	array( 'function' => 'bookmark_link' ),
			'pl_raw'					=>	array( 'function' => 'do_raw' ),
			'pl_video'					=>	array( 'function' => 'pl_video_shortcode' ),
			'pl_child_url'				=>	array( 'function' => 'pl_child_url' ),
			'pl_site_url'				=>	array( 'function' => 'pl_site_url' ),
			'pl_parent_url'				=>	array( 'function' => 'get_themeurl' )
			);

		return $core;
	}

	function do_raw() {

		global $post;
		$str = $post->post_content;

		$start = '[pl_raw]';
		$end = '[/pl_raw]';
		$stpos = strpos( $str, $start );
		if ( $stpos === FALSE )
			return '';
		$stpos += strlen( $start );
		$endpos = strpos( $str, $end, $stpos );
		if ( $endpos === FALSE )
			return '';
		$len = $endpos - $stpos;
		return do_shortcode( substr( $str, $stpos, $len ) );
	}




	/**
	 * 9. This function produces the edit post link for logged in users
	 *
	 * @example <code>[post_edit]</code> is the default usage
	 * @example <code>[post_edit link="Edit", before="<b>" after="</b>"]</code>
	 */
	function pagelines_post_edit_shortcode( $atts ) {

		$defaults = array(
			'link' => __( "<span class='editpage sc'>Edit</span>", 'pagelines' ),
			'before' => '[',
			'after' => ']'
		);
		$atts = shortcode_atts( $defaults, $atts );

		// Prevent automatic WP Output
		ob_start();
		edit_post_link( $atts['link'], $atts['before'], $atts['after'] ); // if logged in
		$edit = ob_get_clean();

		$output = $edit;

		return apply_filters( 'pagelines_post_edit_shortcode', $output, $atts );

	}

	/**
	 * 10. This function produces the category link list
	 *
	 * @example <code>[post_categories]</code> is the default usage
	 * @example <code>[post_categories sep=", "]</code>
	 */
	function pagelines_post_categories_shortcode( $atts ) {

		$defaults = array(
			'sep' => ', ',
			'before' => '',
			'after' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		$cats = get_the_category_list( trim( $atts['sep'] ) . ' ' );

		$output = sprintf( '<span class="categories sc">%2$s%1$s%3$s</span> ', $cats, $atts['before'], $atts['after'] );

		return apply_filters( 'pagelines_post_categories_shortcode', $output, $atts );

	}

	/**
	 * 11. This function produces the tag link list
	 *
	 * @example <code>[post_tags]</code> is the default usage
	 * @example <code>[post_tags sep=", " before="Tags: " after="bar"]</code>
	 */
	function pagelines_post_tags_shortcode( $atts ) {

		$defaults = array(
			'sep' => ', ',
			'before' => __( 'Tagged With: ', 'pagelines' ),
			'after' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		$tags = get_the_tag_list( $atts['before'], trim( $atts['sep'] ) . ' ', $atts['after'] );

		if ( !$tags ) return;

		$output = sprintf( '<span class="tags sc">%s</span> ', $tags );

		return apply_filters( 'pagelines_post_tags_shortcode', $output, $atts );

	}

	/**
	 * 12. This function produces a post type link.
	 *
	 * @example <code>[post_type]</code> is the default usage
	 * @example <code>[post_type before="Type: " after="bar"]</code>
	 */
	function pagelines_post_type_shortcode( $atts ) {

		$defaults = array(
			'before' => __( 'Type: ', 'pagelines' ),
			'after' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		global $post;

		if ( $post->post_type == 'post' )
			return;

		$t = get_post_type_object( $post->post_type );

		$name = $t->labels->name;

		$type = sprintf( '%s%s%s', $atts['before'], $name, $atts['after'] );
		if( $t->has_archive )
			$output = sprintf( '<span class="type sc"><a href="%s">%s</a></span> ', get_post_type_archive_link( $t->name ), $type );
		else
			$output = sprintf( '<span class="type sc">%s</span> ', $type );
		return apply_filters( 'pagelines_post_type_shortcode', $output, $atts );
	}

	/**
	 * 13. This function produces the comment link
	 *
	 * @example <code>[post_comments]</code> is the default usage
	 * @example <code>[post_comments zero="No Comments" one="1 Comment" more="% Comments"]</code>
	 */
	function pagelines_post_comments_shortcode( $atts ) {

		$defaults = array(
			'zero' => __( 'Add Comment', 'pagelines' ),
			'one' => __( "<span class='num'>1</span> Comment", 'pagelines' ),
			'more' => __( "<span class='num'>%</span> Comments", 'pagelines' ),
			'hide_if_off' => 'disabled',
			'before' => '',
			'after' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		if ( ( !comments_open() ) && $atts['hide_if_off'] === 'enabled' )
			return;

		// Prevent automatic WP Output
		ob_start();
		comments_number( $atts['zero'], $atts['one'], $atts['more'] );
		$comments = ob_get_clean();

		$comments = sprintf( '<a href="%s">%s</a>', $this->get_comment_link(), $comments );

		$output = sprintf( '<span class="post-comments sc">%2$s%1$s%3$s</span>', $comments, $atts['before'], $atts['after'] );

		return apply_filters( 'pagelines_post_comments_shortcode', $output, $atts );
	}

	function get_comment_link() {

		$comment = '#comments';

		if( function_exists( 'livefyre_show_comments' ) )
			$comment = '#lf_comment_stream';

		return sprintf( '%s%s', get_permalink(), $comment );
	}

	/**
	 * 14. This function produces the author of the post (link to author archive)
	 *
	 * @example <code>[post_author_posts_link]</code> is the default usage
	 * @example <code>[post_author_posts_link before="<b>" after="</b>"]</code>
	 */
	function pagelines_post_author_posts_link_shortcode( $atts ) {

		$defaults = array(
			'before' => '',
			'after' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		// Prevent automatic WP Output
		ob_start();
		the_author_posts_link();
		$author = ob_get_clean();

		$output = sprintf( '<span class="author vcard sc">%2$s<span class="fn">%1$s</span>%3$s</span>', $author, $atts['before'], $atts['after'] );

		return apply_filters( 'pagelines_post_author_shortcode', $output, $atts );
	}

	/**
	 * 15. This function produces the author of the post (link to author URL)
	 *
	 * @example <code>[post_author_link]</code> is the default usage
	 * @example <code>[post_author_link before="<b>" after="</b>"]</code>
	 */
	function pagelines_post_author_link_shortcode( $atts ) {

		$defaults = array(
			'nofollow' => FALSE,
			'before' => '',
			'after' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		$author = get_the_author();

		//	Link?
		if ( get_the_author_meta( 'url' ) ) {

			//	Build the link
			$author = '<a href="' . get_the_author_meta( 'url' ) . '" title="' . esc_attr( sprintf( __( 'Visit %s&#8217;s website', 'pagelines' ), $author ) ) . '" rel="external">' . $author . '</a>';

		}

		$output = sprintf( '<span class="author vcard sc">%2$s<span class="fn">%1$s</span>%3$s</span>', $author, $atts['before'], $atts['after'] );

		return apply_filters( 'pagelines_post_author_link_shortcode', $output, $atts );

	}

	/**
	 * 16. This function produces the author of the post (display name)
	 *
	 * @example <code>[post_author]</code> is the default usage
	 * @example <code>[post_author before="<b>" after="</b>"]</code>
	 */
	function pagelines_post_author_shortcode( $atts ) {

		$defaults = array(
			'before' => '',
			'after' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		$output = sprintf( '<span class="author vcard sc">%2$s<span class="fn">%1$s</span>%3$s</span>',
						esc_html( get_the_author() ),
						$atts['before'],
						$atts['after']
					);

		return apply_filters( 'pagelines_post_author_shortcode', $output, $atts );

	}

	/**
	 * 17. Post Date
	 *
	 */
	function pagelines_post_date_shortcode( $atts ) {

		$defaults = array(
			'format' => get_option( 'date_format' ),
			'before' => '',
			'after' => '',
			'label' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		$output = sprintf( '<time class="date time published updated sc" datetime="%5$s">%1$s%3$s%4$s%2$s</time> ',
						$atts['before'],
						$atts['after'],
						$atts['label'],
						get_the_time( $atts['format'] ),
						get_the_time( 'c' )
					);

		return apply_filters( 'pagelines_post_date_shortcode', $output, $atts );

	}

	/**
	 * 21. This function/shortcode will show all authors on a post
	 *
	 * @example <code>[show_authors]</code> is the default usage
	 * @example <code>[show_authors]</code>
	 */
	function show_multiple_authors() {

		if( class_exists( 'CoAuthorsIterator' ) ) {

			$i = new CoAuthorsIterator();
			$return = '';
			$i->iterate();
			$return .= '<a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ).'">'.get_the_author_meta( 'display_name' ).'</a>';
			while( $i->iterate() ){
				$return.= $i->is_last() ? ' and ' : ', ';
				$return .= '<a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ).'">'.get_the_author_meta( 'display_name' ).'</a>';
			}

			return $return;

		} else {
			//fallback
		}
	}



	/**
	 * 37. This function produces the time of post publication
	 *
	 * @example <code>[post_time]</code> is the default usage</code>
	 * @example <code>[post_time format="g:i a" before="<b>" after="</b>"]</code>
	 */
	function pagelines_post_time_shortcode( $atts ) {

		$defaults = array(
			'format' => get_option( 'time_format' ),
			'before' => '',
			'after' => '',
			'label' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		$output = sprintf( '<span class="time published sc" title="%5$s">%1$s%3$s%4$s%2$s</span> ',
						$atts['before'],
						$atts['after'],
						$atts['label'],
						get_the_time( $atts['format'] ),
						get_the_time( 'Y-m-d\TH:i:sO' )
						);

		return apply_filters( 'pagelines_post_time_shortcode', $output, $atts );

	}

	function pl_child_url() {
		return get_stylesheet_directory_uri();
	}

	function pl_site_url() {
		return home_url();
	}

	// USAGE: [themeurl]
	function get_themeurl( $atts ) {
		return get_template_directory_uri();
	}

	private function register_shortcodes( $shortcodes ) {

		foreach ( $shortcodes as $shortcode => $data ) {
			if( method_exists( $this, $data['function'] ) )
				add_shortcode( $shortcode, array( &$this, $data['function']) );
		}
	}
//
} // end of class
//
new PageLines_ShortCodes;
