<?php

// get all region slugs in editor
function pl_editor_regions(){

	$regions = array(
		'fixed', 'header', 'footer', 'template'
	);
	
	return $regions;

}

/*
 *	Get index value in array, does shortcodes or default
 */
function pl_array_get( $key, $array, $default = false ){
	
	if( isset( $array[$key] ) && $array[$key] != '' )
		$val = $array[$key];
	else
		$val = $default;
	
	return do_shortcode( $val );
}

/*
 *	Editor functions - Always loaded
 */

function pl_has_editor(){

	return (class_exists('PageLinesTemplateHandler')) ? true : false;

}


// Function to be used w/ compabibility mode to de
function pl_deprecate_v2(){

	if(pl_setting('enable_v2'))
		return false;
	else 
		return true;

}


function pl_use_editor(){
	return true;
}

function pl_less_dev(){	
	if( defined( 'PL_LESS_DEV' ) && PL_LESS_DEV )
		return false; 
	else
		return false;
	
}

function pl_has_dms_plugin(){	
	
	if( class_exists( 'DMSPluginPro' ) )
		return true;
	else 
		return false;	
}

function pl_is_pro(){
	return apply_filters( 'pl_is_pro', false );
}

function pl_pro_text(){	
	return apply_filters( 'pl_pro_text', '' );
}

function pl_pro_disable_class(){
	return apply_filters( 'pl_pro_disable_class', 'hidden' );	
}

function pl_is_activated(){
	return apply_filters( 'pl_is_activated', false );
}

// Process old function type to new format
function process_to_new_option_format( $old_options ){

	$new_options = array();

	foreach($old_options as $key => $o){

		if($o['type'] == 'multi_option' || $o['type'] == 'text_multi'){

			$sub_options = array();
			foreach($o['selectvalues'] as $sub_key => $sub_o){
				$sub_options[ ] = process_old_opt($sub_key, $sub_o, $o);
			}
			$new_options[ ] = array(
				'type' 	=> 'multi',
				'title'	=> $o['title'],
				'opts'	=> $sub_options
			);
		} else {
			$new_options[ ] = process_old_opt($key, $o);
		}

	}

	return $new_options;
}

function process_old_opt( $key, $old, $otop = array()){

	if(isset($otop['type']) && $otop['type'] == 'text_multi')
		$old['type'] = 'text';

	$defaults = array(
        'type' 			=> 'check',
		'title'			=> '',
		'inputlabel'	=> '',
		'exp'			=> '',
		'shortexp'		=> '',
		'count_start'	=> 0,
		'count_number'	=> '',
		'selectvalues'	=> array(),
		'taxonomy_id'	=> '',
		'post_type'		=> '',
		'span'			=> 1,
		'col'			=> 1,
		'default'		=> '',
	);

	$old = wp_parse_args($old, $defaults);

	$exp = ($old['exp'] == '' && $old['shortexp'] != '') ? $old['shortexp'] : $old['exp'];

	if($old['type'] == 'text_small'){
		$type = 'text';
	} elseif($old['type'] == 'colorpicker'){
		$type = 'color';
	} elseif($old['type'] == 'check_multi'){
		$type = 'multi';
		
		foreach($old['selectvalues'] as $key => &$info){
			$info['type'] = 'check';
		}
	} else
		$type = $old['type'];

	$new = array(
		'key'			=> ( !isset($old['key']) ) ? $key : $old['key'],
		'title'			=> $old['title'],
		'label'			=> ( !isset($old['label']) && isset($old['inputlabel'])) ? $old['inputlabel'] : $old['label'],
		'type'			=> $type,
		'help'			=> $exp,
		'opts'			=> ( !isset($old['opts']) && isset($old['selectvalues'])) ? $old['selectvalues'] : $old['opts'],
		'span'			=> $old['span'],
		'col'			=> $old['col']
	);

	if ( isset( $old['scope'] ) )
		$new['scope'] = $old['scope'];
	
	if ( isset( $old['template'] ) )
		$new['template'] = $old['template'];

	if($old['type'] == 'count_select'){
		$new['count_start'] = $old['count_start'];
		$new['count_number'] = $old['count_number'];
	}

	if($old['taxonomy_id'] != ''){
		$new['taxonomy_id'] = $old['taxonomy_id'];
	}	

	if($old['post_type'] != '')
		$new['post_type'] = $old['post_type'];
		
	if($old['default'] != '')
		$new['default'] = $old['default'];

	return $new;
}

function pl_create_id( $string ){

	if( ! empty($string) ){
		$string = str_replace( ' ', '_', trim( strtolower( $string ) ) );
		$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
	} else 
		$string = pl_new_clone_id();
	
	return ( ! is_int($string) ) ? $string : 's'.$string;
}

function pl_new_clone_id(){
	return 'u' . substr(uniqid(), -5);
}


function pl_create_int_from_string( $str ){
	
	return (int) substr( preg_replace("/[^0-9,.]/", "", md5( $str )), -6);
}


/*
 * Lets document utility functions
 */
function pl_add_query_arg( $args ) {

	global $wp;
	$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
	return add_query_arg( $args, $current_url );
}

/*
 * This function recursively converts an multi dimensional array into a multi layer object
 * Needed for json conversion in < php 5.2
 */
function pl_arrays_to_objects( array $array ) {

	$objects = new stdClass;

	if( is_array($array) ){
		foreach ( $array as $key => $val ) {

			if($key === ''){
				$key = 0;
			}

	        if ( is_array( $val ) && !empty( $val )) {


				$objects->{$key} = pl_arrays_to_objects( $val );

	        } else {

	            $objects->{$key} = $val;

	        }
	    }

	}

    return $objects;
}

function pl_animation_array(){
	$animations = array(
		'no-anim'			=> __( 'No Animation', 'pagelines' ),
		'pla-fade'			=> __( 'Fade', 'pagelines' ),
		'pla-scale'			=> __( 'Scale', 'pagelines' ),
		'pla-from-left'		=> __( 'From Left', 'pagelines' ),
		'pla-from-right'	=> __( 'From Right', 'pagelines' ), 
		'pla-from-bottom'	=> __( 'From Bottom', 'pagelines' ), 
		'pla-from-top'		=> __( 'From Top', 'pagelines' ), 
	); 
	
	return $animations;
}

function pl_get_all_taxonomies(){
	$args = array(
	  'public'   => true,

	);
	return get_taxonomies( $args,'names');
}

function pl_icon_array(){

	$icons = array(
		'glass-music',
		'search',
		'envelope-o',
		'heart',
		'star',
		'star-o',
		'user',
		'film',
		'th-large',
		'th',
		'th-list',
		'check',
		'times',
		'search-plus',
		'search-minus',
		'power-off',
		'signal',
		'gear',
		'cog',
		'trash-o',
		'home',
		'file-o',
		'clock-o',
		'road',
		'download',
		'arrow-circle-o-down',
		'arrow-circle-o-up',
		'inbox',
		'play-circle-o',
		'rotate-right',
		'repeat',
		'refresh',
		'list-alt',
		'lock',
		'flag',
		'headphones',
		'volume-off',
		'volume-down',
		'volume-up',
		'qrcode',
		'barcode',
		'tag',
		'tags',
		'book',
		'bookmark',
		'print',
		'camera',
		'font',
		'bold',
		'italic',
		'text-height',
		'text-width',
		'align-left',
		'align-center',
		'align-right',
		'align-justify',
		'list',
		'dedent',
		'outdent',
		'indent',
		'video-camera',
		'photo',
		'image',
		'picture-o',
		'pencil',
		'map-marker',
		'adjust',
		'tint',
		'edit',
		'pencil-square-o',
		'share-square-o',
		'check-square-o',
		'arrows',
		'step-backward',
		'fast-backward',
		'backward',
		'play',
		'pause',
		'stop',
		'forward',
		'fast-forward',
		'step-forward',
		'eject',
		'chevron-left',
		'chevron-right',
		'plus-circle',
		'minus-circle',
		'times-circle',
		'check-circle',
		'question-circle',
		'info-circle',
		'crosshairs',
		'times-circle-o',
		'check-circle-o',
		'ban',
		'arrow-left',
		'arrow-right',
		'arrow-up',
		'arrow-down',
		'mail-forward',
		'share',
		'expand',
		'compress',
		'plus',
		'minus',
		'asterisk',
		'exclamation-circle',
		'gift',
		'leaf',
		'fire',
		'eye',
		'eye-slash',
		'warning',
		'exclamation-triangle',
		'plane',
		'calendar',
		'random',
		'comment',
		'magnet',
		'chevron-up',
		'chevron-down',
		'retweet',
		'shopping-cart',
		'folder',
		'folder-open',
		'arrows-v',
		'arrows-h',
		'bar-chart-o',
		'twitter-square',
		'facebook-square',
		'camera-retro',
		'key',
		'gears',
		'cogs',
		'comments',
		'thumbs-o-up',
		'thumbs-o-down',
		'star-half',
		'heart-o',
		'sign-out',
		'linkedin-square',
		'thumb-tack',
		'external-link',
		'sign-in',
		'trophy',
		'github-square',
		'upload',
		'lemon-o',
		'phone',
		'square-o',
		'bookmark-o',
		'phone-square',
		'twitter',
		'facebook',
		'github',
		'unlock',
		'credit-card',
		'rss',
		'hdd-o',
		'bullhorn',
		'bell',
		'certificate',
		'hand-o-right',
		'hand-o-left',
		'hand-o-up',
		'hand-o-down',
		'arrow-circle-left',
		'arrow-circle-right',
		'arrow-circle-up',
		'arrow-circle-down',
		'globe',
		'wrench',
		'tasks',
		'filter',
		'briefcase',
		'arrows-alt',
		'group',
		'users',
		'chain',
		'link',
		'cloud',
		'flask',
		'cut',
		'scissors',
		'copy',
		'files-o',
		'paperclip',
		'save',
		'floppy-o',
		'square',
		'navicon',
		'reorder',
		'bars',
		'list-ul',
		'list-ol',
		'strikethrough',
		'underline',
		'table',
		'magic',
		'truck',
		'pinterest',
		'pinterest-square',
		'google-plus-square',
		'google-plus',
		'money',
		'caret-down',
		'caret-up',
		'caret-left',
		'caret-right',
		'columns',
		'unsorted',
		'sort',
		'sort-down',
		'sort-desc',
		'sort-up',
		'sort-asc',
		'envelope',
		'linkedin',
		'rotate-left',
		'undo',
		'legal',
		'gavel',
		'dashboard',
		'tachometer',
		'comment-o',
		'comments-o',
		'flash',
		'bolt',
		'sitemap',
		'umbrella',
		'paste',
		'clipboard',
		'lightbulb-o',
		'exchange',
		'cloud-download',
		'cloud-upload',
		'user-md',
		'stethoscope',
		'suitcase',
		'bell-o',
		'coffee',
		'cutlery',
		'file-text-o',
		'building-o',
		'hospital-o',
		'ambulance',
		'medkit',
		'fighter-jet',
		'beer',
		'h-square',
		'plus-square',
		'angle-double-left',
		'angle-double-right',
		'angle-double-up',
		'angle-double-down',
		'angle-left',
		'angle-right',
		'angle-up',
		'angle-down',
		'desktop',
		'laptop',
		'tablet',
		'mobile-phone',
		'mobile',
		'circle-o',
		'quote-left',
		'quote-right',
		'spinner',
		'circle',
		'mail-reply',
		'reply',
		'github-alt',
		'folder-o',
		'folder-open-o',
		'smile-o',
		'frown-o',
		'meh-o',
		'gamepad',
		'keyboard-o',
		'flag-o',
		'flag-checkered',
		'terminal',
		'code',
		'mail-reply-all',
		'reply-all',
		'star-half-empty',
		'star-half-full',
		'star-half-o',
		'location-arrow',
		'crop',
		'code-fork',
		'unlink',
		'chain-broken',
		'question',
		'info',
		'exclamation',
		'superscript',
		'subscript',
		'eraser',
		'puzzle-piece',
		'microphone',
		'microphone-slash',
		'shield',
		'calendar-o',
		'fire-extinguisher',
		'rocket',
		'maxcdn',
		'chevron-circle-left',
		'chevron-circle-right',
		'chevron-circle-up',
		'chevron-circle-down',
		'html5',
		'css3',
		'anchor',
		'unlock-alt',
		'bullseye',
		'ellipsis-h',
		'ellipsis-v',
		'rss-square',
		'play-circle',
		'ticket',
		'minus-square',
		'minus-square-o',
		'level-up',
		'level-down',
		'check-square',
		'pencil-square',
		'external-link-square',
		'share-square',
		'compass',
		'toggle-down',
		'caret-square-o-down',
		'toggle-up',
		'caret-square-o-up',
		'toggle-right',
		'caret-square-o-right',
		'euro',
		'eur',
		'gbp',
		'dollar',
		'usd',
		'rupee',
		'inr',
		'cny',
		'rmb',
		'yen',
		'jpy',
		'ruble',
		'rouble',
		'rub',
		'won',
		'krw',
		'bitcoin',
		'btc',
		'file',
		'file-text',
		'sort-alpha-asc',
		'sort-alpha-desc',
		'sort-amount-asc',
		'sort-amount-desc',
		'sort-numeric-asc',
		'sort-numeric-desc',
		'thumbs-up',
		'thumbs-down',
		'youtube-square',
		'youtube',
		'xing',
		'xing-square',
		'youtube-play',
		'dropbox',
		'stack-overflow',
		'instagram',
		'flickr',
		'adn',
		'bitbucket',
		'bitbucket-square',
		'tumblr',
		'tumblr-square',
		'long-arrow-down',
		'long-arrow-up',
		'long-arrow-left',
		'long-arrow-right',
		'apple',
		'windows',
		'android',
		'linux',
		'dribbble',
		'skype',
		'foursquare',
		'trello',
		'female',
		'male',
		'gittip',
		'sun-o',
		'moon-o',
		'archive',
		'bug',
		'vk',
		'weibo',
		'renren',
		'pagelines',
		'stack-exchange',
		'arrow-circle-o-right',
		'arrow-circle-o-left',
		'toggle-left',
		'caret-square-o-left',
		'dot-circle-o',
		'wheelchair',
		'vimeo-square',
		'turkish-lira',
		'try',
		'plus-square-o',
		'space-shuttle',
		'slack',
		'envelope-square',
		'wordpress',
		'openid',
		'institution',
		'bank',
		'university',
		'mortar-board',
		'graduation-cap',
		'yahoo',
		'google',
		'reddit',
		'reddit-square',
		'stumbleupon-circle',
		'stumbleupon',
		'delicious',
		'digg',
		'pied-piper-square',
		'pied-piper',
		'pied-piper-alt',
		'drupal',
		'joomla',
		'language',
		'fax',
		'building',
		'child',
		'paw',
		'spoon',
		'cube',
		'cubes',
		'behance',
		'behance-square',
		'steam',
		'steam-square',
		'recycle',
		'automobile',
		'car',
		'cab',
		'taxi',
		'tree',
		'spotify',
		'deviantart',
		'soundcloud',
		'database',
		'file-pdf-o',
		'file-word-o',
		'file-excel-o',
		'file-powerpoint-o',
		'file-photo-o',
		'file-picture-o',
		'file-image-o',
		'file-zip-o',
		'file-archive-o',
		'file-sound-o',
		'file-audio-o',
		'file-movie-o',
		'file-video-o',
		'file-code-o',
		'vine',
		'codepen',
		'jsfiddle',
		'life-bouy',
		'life-saver',
		'support',
		'life-ring',
		'circle-o-notch',
		'ra',
		'rebel',
		'ge',
		'empire',
		'git-square',
		'git',
		'hacker-news',
		'tencent-weibo',
		'qq',
		'wechat',
		'weixin',
		'send',
		'paper-plane',
		'send-o',
		'paper-plane-o',
		'history',
		'circle-thin',
		'header',
		'paragraph',
		'sliders',
		'share-alt',
		'share-alt-square',
		'bomb',
		'remove',
		'reorder',
		'plus-sign',
		'ok',
		'trash',
		'fullscreen',
		'info-sign',
		'sun',
		'picture',
		'star-empty',
		'zoom-in',
		'zoom-out',
		'off',
		'file-alt',
		'download-alt',
		'indent-left',
		'indent-right',
		'facetime-video',
		'ban-circle',
		'bar-chart',
		'beaker',
		'bell-alt',
		'bitbucket-sign',
		'bookmark-empty',
		'building',
		'calendar-empty',
		'check-empty',
		'check-minus',
		'check-sign',
		'chevron-sign-down',
		'chevron-sign-up',
		'chevron-sign-left',
		'chevron-sign-right',
		'circle-arrow-down',
		'circle-arrow-up',
		'circle-arrow-left',
		'circle-arrow-right',
		'circle-blank',
		'collapse',
		'collapse-top',
		'collapse-alt',
		'comment-alt',
		'comments-alt',
		'double-angle-down',
		'double-angle-up',
		'double-angle-left',
		'double-angle-right',
		'edit-sign',
		'ellipsis-horizontal',
		'ellipsis-vertical',
		'envelope-alt',
		'exclamation-sign',
		'eye-open',
		'eye-close',
		'facebook-sign',
		'file-text-alt',
		'flag-alt',
		'food',
		'frown',
		'github-sign',
		'google-plus-sign',
		'h-sign',
		'hand-up',
		'hand-down',
		'hand-left',
		'hand-right',
		'hdd',
		'heart-empty',
		'hospital',
		'keyboard',
		'lemon',
		'lightbulb',
		'linkedin-sign',
		'meh',
		'microphone-off',
		'minus-sign',
		'minus-sign-alt',
		'moon',
		'move',
		'ok-circle',
		'ok-sign',
		'paper-clip',
		'pinterest-sign',
		'pushpin',
		'question-sign',
		'remove-sign',
		'remove-circle',
		'renminbi',
		'resize-full',
		'resize-small',
		'resize-horizontal',
		'resize-vertical',
		'rss-sign',
		'screenshot',
		'share-sign',
		'sign-blank',
		'signout',
		'smile',
		'stackexchange',
		'thumbs-down-alt',
		'thumbs-up-alt',
		'time',
		'tumblr-sign',
		'twitter-sign',
		'unchecked',
		'upload-alt',
		'warning-sign',
		'youtube-sign'
	);	
	asort($icons);
	
	$icons = array_values($icons);
	
	return apply_filters( 'pl_icon_array', $icons );
}

function pl_button_classes(){
	$array = array(
		''			 		=> 'Default',
		'btn-link-color'	=> 'Link Color',
		'btn-ol-white'		=> 'Outline White',
		'btn-ol-black'		=> 'Outline Black',
		'btn-ol-link'		=> 'Outline Link',
		'btn-primary'		=> 'Dark Blue',
		'btn-info'			=> 'Light Blue',
		'btn-success'		=> 'Green',
		'btn-warning'		=> 'Orange',
		'btn-important'		=> 'Red',
		'btn-inverse'		=> 'Black',
	); 
	return $array;
}

function pl_theme_classes(){
	$array = array(
		''			 	=> 'Default',
		'pl-trans'		=> 'No Background',
		'pl-contrast'	=> 'Contrast BG',
		'pl-black'		=> 'Black Background, white text',
		'pl-grey'		=> 'Dark Grey Background, White Text',
		'pl-white'		=> 'White Background, Black Text',
		'pl-dark-img'	=> 'Black Background, White Text w Shadow',
		'pl-light-img'	=> 'White Background, Black Text w Shadow',
		'pl-base'		=> 'Base Color Background',
	); 
	return apply_filters( 'pl_theme_classes', $array );
}

function pl_get_area_classes( $section, $set = array(), $namespace = false ){

	$namespace = ( $namespace ) ? $namespace : $section->id;

	if( 'navbar' == $namespace )
		$namespace = 'navbar_area';

	$class = array(
		'theme'		=> $section->opt($namespace.'_theme'),
		'scroll'	=> $section->opt($namespace.'_scroll'),
		'video'		=> ( $section->opt($namespace.'_video') ) ? 'bg-video-canvas' : '',
		'repeat'	=> ( $section->opt($namespace.'_repeat') ) ? 'pl-bg-repeat' : 'pl-bg-cover'
	);
	
	$class = wp_parse_args( $set, $class );
	
	return join(' ', $class);

}

function pl_get_area_styles( $section, $namespace = false ){

	$namespace = ( $namespace ) ? $namespace : $section->id;

	$bg = $section->opt($namespace.'_background');
	
	$color = $section->opt($namespace.'_color');
	
	$color_enable = $section->opt($namespace.'_color_enable');

	$style = array(
		'background' => ( $bg ) ? sprintf('background-image: url(%s);', $bg) : '',
		'color'		=> ( $color_enable ) ? sprintf('background-color: %s;', pl_hash($color)) : '',
	); 
	
	return $style;
	
	
}

function pl_standard_video_bg( $section, $namespace = false ){
	
	$namespace = ( $namespace ) ? $namespace : $section->id;
	$video = '';
	if( $section->opt( $namespace.'_video') ){
		
		$videos = pl_get_video_sources( array( $section->opt( $namespace.'_video'), $section->opt( $namespace.'_video_2') ) );
		$video = sprintf(
			'<div class="bg-video-viewport"><video poster="%s" class="bg-video" autoplay loop>%s</video></div>', 
			pl_transparent_image(), 
			$videos
		);

		return $video;
	} else
		return '';
		
}

function pl_get_background_options( $section, $column = 3 ){
	
	$namespace = $section->id;
	
	$options = array(
		'title' => __( 'Background Options', 'pagelines' ),
		'type'	=> 'multi',
		'col'	=> $column,
		'opts'	=> array(
			array(
				'key'			=> $namespace.'_background',
				'type' 			=> 'image_upload',
				'label' 		=> __( 'Background Image', 'pagelines' ),
			),
			array(
				'key'			=> $namespace.'_repeat',
				'type' 			=> 'check',
				'label' 		=> __( 'Repeat Background?', 'pagelines' ),
			),
			array(
				'key'			=> $namespace.'_theme',
				'type' 			=> 'select_theme',
				'label' 		=> __( 'Background Theme', 'pagelines' ),
			),
			array(
				'key'			=> $namespace.'_video',
				'type' 			=> 'media_select_video',
				'label' 		=> __( 'Background Video', 'pagelines' ),
			),
			array(
				'key'			=> $namespace.'_color_enable',
				'type' 			=> 'check',
				'label' 		=> __( 'Background Color Enable', 'pagelines' ),
			),
			array(
				'key'			=> $namespace.'_color',
				'type' 			=> 'color',
				'label' 		=> __( 'Background Color', 'pagelines' ),
			),
		)
	);
	
	
	return $options;
}


function pl_get_post_type_options( ){
	

	$opts = array(
			array(
				'key'			=> 'post_type',
				'type' 			=> 'select',
				'opts'			=> pl_get_thumb_post_types( false ),
				'label' 	=> __( 'Select Post Type', 'pagelines' ),
			),
			array(
				'key'			=> 'post_total',
				'type' 			=> 'count_select',
				'count_start'	=> 5,
				'count_number'	=> 20,
				'default'		=> 10,
				'label' 		=> __( 'Total Posts Loaded', 'pagelines' ),
			),
			array(
				'key'		=> 'post_sort',
				'type'		=> 'select',
				'label'		=> __( 'Element Sorting', 'pagelines' ),
				'default'	=> 'DESC',
				'opts'			=> array(
					'DESC'		=> array('name' => __( 'Date Descending (default)', 'pagelines' ) ),
					'ASC'		=> array('name' => __( 'Date Ascending', 'pagelines' ) ),
					'rand'		=> array('name'	=> __( 'Random', 'pagelines' ) )
				)
			),
			array(
				'key'			=> 'meta_key',
				'type' 			=> 'text_small',
				'label' 	=> __( 'Meta Key', 'pagelines' ),	
			),
			array(
				'key'			=> 'meta_value',
				'type' 			=> 'text_small',
				'label' 	=> __( 'Meta Key Value', 'pagelines' ),
				'help'		=> __( 'Select only posts which have a certain meta key and corresponding meta value. Useful for featured posts, or similar.', 'pagelines' ),
			),
		);
	
	
	return $opts;
}



function get_sidebar_select(){


	global $wp_registered_sidebars;
	$allsidebars = $wp_registered_sidebars;
	ksort($allsidebars);

	$sidebar_select = array();
	foreach($allsidebars as $key => $sb){

		$sidebar_select[ $sb['id'] ] = array( 'name' => $sb['name'] );
	}

	return $sidebar_select;
}

function pl_count_sidebar_widgets( $sidebar_id ){

	$total_widgets = wp_get_sidebars_widgets();

	if(isset($total_widgets[ $sidebar_id ]))
		return count( $total_widgets[ $sidebar_id ] );
	else
		return false;
}

function pl_enqueue_script(  $handle, $src = false, $deps = array(), $ver = false, $in_footer = false ){
	
	global $wp_scripts;
	
	wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
}

function pl_add_theme_tab( $array ){
	
	global $pl_user_theme_tabs;
	
	if(!isset($pl_user_theme_tabs) || !is_array($pl_user_theme_tabs))
		$pl_user_theme_tabs = array(); 
		
		
	$pl_user_theme_tabs = array_merge($array, $pl_user_theme_tabs); 
	
	
	
}




function pl_blank_template( $name = '' ){
	if ( current_user_can( 'edit_theme_options' ) )
		return sprintf('<div class="blank-section-template pl-editor-only"><strong>%s</strong> is hidden or returned no output.</div>', $name);
	else 
		return '';
	
}


function pl_shortcodize_url( $full_url ){
	$url = str_replace(home_url(), '[pl_site_url]', $full_url);
	
	return $url;
}

function pl_get_image_sizes() {
	$sizes = get_intermediate_image_sizes();
	$sizes[] = 'full';
	return $sizes;
}

function pl_check_updater_exists() {
	$path = sprintf( '%s/pagelines-updater/pagelines-updater.php', WP_PLUGIN_DIR );
	return ( is_file( $path ) ) ? true : false;
}
