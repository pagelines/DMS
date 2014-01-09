<?php 

	
add_action('add_meta_boxes', 'pagelines_metabox_posts');
function pagelines_metabox_posts(){
	
	$meta_box = array(
		'id' => 'nectar-metabox-post-gallery',
		'title' =>  __('Gallery Configuration', 'pagelines'),
		'description' => 'Once you\'ve inserted a WordPress gallery using the "Add Media" button above, you can use the gallery slider checkbox below to transform your images into a slider.',
		'post_type' => 'post',
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			array(
					'name' =>  __('Gallery Slider', 'pagelines'),
					'desc' => __('Would you like to turn your gallery into a slider?', 'pagelines'),
					'id' => '_nectar_gallery_slider',
					'type' => 'checkbox',
                    'std' => 1
				)
		)
	);
	$callback = create_function( '$post,$meta_box', 'nectar_create_meta_box( $post, $meta_box["args"] );' );
    add_meta_box( $meta_box['id'], $meta_box['title'], $callback, $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
	
	
	#-----------------------------------------------------------------#
	# Quote
	#-----------------------------------------------------------------# 
    $meta_box = array(
		'id' => 'nectar-metabox-post-quote',
		'title' =>  __('Quote Settings', 'pagelines'),
		'description' => '',
		'post_type' => 'post',
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			array(
					'name' =>  __('Quote Content', 'pagelines'),
					'desc' => __('Please type the text for your quote here.', 'pagelines'),
					'id' => '_nectar_quote',
					'type' => 'textarea',
                    'std' => ''
				)
		)
	);
    add_meta_box( $meta_box['id'], $meta_box['title'], $callback, $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
	
	#-----------------------------------------------------------------#
	# Link
	#-----------------------------------------------------------------# 
	$meta_box = array(
		'id' => 'nectar-metabox-post-link',
		'title' =>  __('Link Settings', 'pagelines'),
		'description' => '',
		'post_type' => 'post',
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			array(
					'name' =>  __('Link URL', 'pagelines'),
					'desc' => __('Please input the URL for your link. I.e. http://www.themenectar.com', 'pagelines'),
					'id' => '_nectar_link',
					'type' => 'text',
					'std' => ''
				)
		)
	);
    add_meta_box( $meta_box['id'], $meta_box['title'], $callback, $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
    
	#-----------------------------------------------------------------#
	# Video
	#-----------------------------------------------------------------# 
    $meta_box = array(
		'id' => 'nectar-metabox-post-video',
		'title' => __('Video Settings', 'nectar'),
		'description' => '',
		'post_type' => 'post',
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			array( 
				'name' => __('M4V File URL', 'pagelines'),
				'desc' => __('Please upload the .m4v video file. <br/><strong>You must include both formats.</strong>', 'pagelines'),
				'id' => '_nectar_video_m4v',
				'type' => 'media', 
				'std' => ''
			),
			array( 
					'name' => __('OGV File URL', 'pagelines'),
					'desc' => __('Please upload the .ogv video file  <br/><strong>You must include both formats.</strong>', 'pagelines'),
					'id' => '_nectar_video_ogv',
					'type' => 'media',
					'std' => ''
				),
			array( 
					'name' => __('Preview Image', 'pagelines'),
					'desc' => __('Image should be at least 680px wide. Click the "Upload" button to begin uploading your image, followed by "Select File" once you have made your selection. Only applies to self hosted videos.', 'pagelines'),
					'id' => '_nectar_video_poster',
					'type' => 'file',
					'std' => ''
				),
			array(
					'name' => __('Embedded Code', 'pagelines'),
					'desc' => __('If the video is an embed rather than self hosted, enter in a Vimeo or Youtube embed code here. <strong> Embeds work worse with the parallax effect, but if you must use this, Vimeo is recommended. </strong> ', 'pagelines'),
					'id' => '_nectar_video_embed',
					'type' => 'textarea',
					'std' => ''
				)
		)
	);
	add_meta_box( $meta_box['id'], $meta_box['title'], $callback, $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
	
	#-----------------------------------------------------------------#
	# Audio
	#-----------------------------------------------------------------# 
	$meta_box = array(
		'id' => 'nectar-metabox-post-audio',
		'title' =>  __('Audio Settings', 'pagelines'),
		'description' => '',
		'post_type' => 'post',
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			array( 
				'name' => __('MP3 File URL', 'pagelines'),
				'desc' => __('Please enter in the URL to the .mp3 file', 'pagelines'),
				'id' => '_nectar_audio_mp3',
				'type' => 'text',
				'std' => ''
			),
			array( 
					'name' => __('OGA File URL', 'pagelines'),
					'desc' => __('Please enter in the URL to the .ogg or .oga file', 'pagelines'),
					'id' => '_nectar_audio_ogg',
					'type' => 'text',
					'std' => ''
				)
		)
	);
	add_meta_box( $meta_box['id'], $meta_box['title'], $callback, $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
}


#-----------------------------------------------------------------#
# Create Meta
#-----------------------------------------------------------------#
function nectar_create_meta_box( $post, $meta_box )
{
	
    if( !is_array($meta_box) ) return false;
    
    if( isset($meta_box['description']) && $meta_box['description'] != '' ){
    	echo '<p>'. $meta_box['description'] .'</p>';
    }
    
	wp_nonce_field( basename(__FILE__), 'nectar_meta_box_nonce' );
	echo '<table class="form-table nectar-metabox-table">';
 	
	$count = 0;
	
	foreach( $meta_box['fields'] as $field ){

		$meta = get_post_meta( $post->ID, $field['id'], true );
		
		$inline = null;
		if(isset($field['extra'])) { $inline = true; }
		
		if($inline == null) {
			
		echo '<tr><th><label for="'. $field['id'] .'"><strong>'. $field['name'] .'</strong>
			  <span>'. $field['desc'] .'</span></label></th>';
		}

		
		switch( $field['type'] ){	
			case 'text': 
				echo '<td><input type="text" name="nectar_meta['. $field['id'] .']" id="'. $field['id'] .'" value="'. ($meta ? $meta : $field['std']) .'" size="30" /></td>';
				break;	
				
			case 'textarea':
				echo '<td><textarea name="nectar_meta['. $field['id'] .']" id="'. $field['id'] .'" rows="8" cols="5">'. ($meta ? $meta : $field['std']) .'</textarea></td>';
				break;
			case 'media_textarea':
				echo '<td><div style="display:none;" class="attr_placeholder" data-poster="" data-media-mp4="" data-media-ogv=""></div><textarea name="nectar_meta['. $field['id'] .']" id="'. $field['id'] .'" rows="8" cols="5">'. ($meta ? $meta : $field['std']) .'</textarea></td>';
				break;
			
			case 'editor' :
				$settings = array(
		            'textarea_name' => 'nectar_meta['. $field['id'] .']',
		            'editor_class' => '',
		            'wpautop' => true
		        );
		        wp_editor($meta, $field['id'], $settings );
				
				break;
			case 'file':
				 
				echo '<td><input type="hidden" id="' . $field['id'] . '" name="nectar_meta[' . $field['id'] . ']" value="' . ($meta ? $meta : $field['std']) . '" />';
		        echo '<img class="redux-opts-screenshot" id="redux-opts-screenshot-' . $field['id'] . '" src="' . ($meta ? $meta : $field['std']) . '" />';
		        if( ($meta ? $meta : $field['std']) == '') {$remove = ' style="display:none;"'; $upload = ''; } else {$remove = ''; $upload = ' style="display:none;"'; }
		        echo ' <a data-update="Select File" data-choose="Choose a File" href="javascript:void(0);"class="redux-opts-upload button-secondary"' . $upload . ' rel-id="' . $field['id'] . '">' . __('Upload', 'pagelines') . '</a>';
		        echo ' <a href="javascript:void(0);" class="redux-opts-upload-remove"' . $remove . ' rel-id="' . $field['id'] . '">' . __('Remove Upload', 'pagelines') . '</a></td>';
		        
				break;
 			
			case 'media':
				 
				echo '<td><input type="text" class="file_display_text" id="' . $field['id'] . '" name="nectar_meta[' . $field['id'] . ']" value="' . ($meta ? $meta : $field['std']) . '" />';
		        if( ($meta ? $meta : $field['std']) == '') {$remove = ' style="display:none;"'; $upload = ''; } else {$remove = ''; $upload = ' style="display:none;"'; }
		        echo ' <a data-update="Select File" data-choose="Choose a File" href="javascript:void(0);"class="redux-opts-media-upload button-secondary"' . $upload . ' rel-id="' . $field['id'] . '">' . __('Add Media', 'pagelines') . '</a>';
		        echo ' <a href="javascript:void(0);" class="redux-opts-upload-media-remove"' . $remove . ' rel-id="' . $field['id'] . '">' . __('Remove Media', 'pagelines') . '</a></td>';
		        
				break;
				
			case 'images':
			    echo '<td><input type="button" class="button" name="' . $field['id'] . '" id="nectar_images_upload" value="' . $field['std'] .'" /></td>';
			    break;
				
			case 'select':
				echo'<td><select name="nectar_meta['. $field['id'] .']" id="'. $field['id'] .'">';
				foreach( $field['options'] as $key => $option ){
					echo '<option value="' . $key . '"';
					if( $meta ){ 
						if( $meta == $key ) echo ' selected="selected"'; 
					} else {
						if( $field['std'] == $key ) echo ' selected="selected"'; 
					}
					echo'>'. $option .'</option>';
				}
				echo'</select></td>';
				break;
			case 'choice_below' :
				
				wp_register_style(
	                'redux-opts-jquery-ui-custom-css',
	                apply_filters('redux-opts-ui-theme',  NECTAR_FRAMEWORK_DIRECTORY . 'options/css/custom-theme/jquery-ui-1.10.0.custom.css'),
	                '',
	                time(),
	                'all'
	            );
				 wp_enqueue_style('redux-opts-jquery-ui-custom-css');
		         wp_enqueue_script(
		            'redux-opts-field-button_set-js', 
		            NECTAR_FRAMEWORK_DIRECTORY . 'options/fields/button_set/field_button_set.js', 
		            array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'),
		            time(),
		            true
		        );
				echo '<td colspan="8">';
				    echo '<fieldset class="buttonset">';
						foreach( $field['options'] as $key => $option ){
				
							echo '<input type="radio" id="nectar_meta_'. $key .'" name="nectar_meta['. $field["id"] .']" value="'. $key .'" ';
							if( $meta ){ 
								if( $meta == $key ) echo ' checked="checked"'; 
							} else {
								if( $field['std'] == $key ) echo ' checked="checked"';
							}
							echo ' /> ';
							echo '<label for="nectar_meta_'. $key .'"> '.$option.'</label>';
							
						}
					echo '</fieldset>';
				echo '</td>';
				break;
			case 'multi-select':
				echo'<td><select multiple="multiple" name="nectar_meta['. $field['id'] .'][]" id="'. $field['id'] .'">';
				foreach( $field['options'] as $key => $option ){
					echo '<option value="' . $key . '"';
					if( $meta ){
						
						echo (is_array($meta) && in_array($key, $meta)) ? ' selected="selected"' : '';
           				 
						if( $meta == $key ) echo ' selected="selected"'; 
					} else {
						if( $field['std'] == $key ) echo ' selected="selected"'; 
					}
					echo'>'. $option .'</option>';
				}
				echo'</select></td>';
				break;
				
			case 'slide_alignment' :
				
				wp_register_style(
	                'redux-opts-jquery-ui-custom-css',
	                apply_filters('redux-opts-ui-theme',  NECTAR_FRAMEWORK_DIRECTORY . 'options/css/custom-theme/jquery-ui-1.10.0.custom.css'),
	                '',
	                time(),
	                'all'
	            );
				 wp_enqueue_style('redux-opts-jquery-ui-custom-css');
		         wp_enqueue_script(
		            'redux-opts-field-button_set-js', 
		            NECTAR_FRAMEWORK_DIRECTORY . 'options/fields/button_set/field_button_set.js', 
		            array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'),
		            time(),
		            true
		        );
				echo '<td>';
				    echo '<fieldset class="buttonset">';
						foreach( $field['options'] as $key => $option ){
				
							echo '<input type="radio" id="nectar_meta_'. $key .'" name="nectar_meta['. $field["id"] .']" value="'. $key .'" ';
							if( $meta ){ 
								if( $meta == $key ) echo ' checked="checked"'; 
							} else {
								if( $field['std'] == $key ) echo ' checked="checked"';
							}
							echo ' /> ';
							echo '<label for="nectar_meta_'. $key .'"> '.$option.'</label>';
							
						}
					echo '</fieldset>';
				echo '</td>';
				break;
			case 'radio':
				echo '<td>';
				foreach( $field['options'] as $key => $option ){
					echo '<label class="radio-label"><input type="radio" name="nectar_meta['. $field['id'] .']" value="'. $key .'" class="radio"';
					if( $meta ){ 
						if( $meta == $key ) echo ' checked="checked"'; 
					} else {
						if( $field['std'] == $key ) echo ' checked="checked"';
					}
					echo ' /> '. $option .'</label> ';
				}
				echo '</td>';
				break;
			case 'slider_button_text':
				if($field['extra'] == 'first'){
					$count++;
					echo '<tr><td><label><strong>Button #'.$count.'</strong> <span>Configure your button here.</span> </label></td>';
				}
				echo '<td class="inline">';
				if($inline != null) {
					echo '<label for="'. $field['id'] .'"><strong>'. $field['name'] .'</strong>
			 		 <span>'. $field['desc'] .'</span></label>';
				}
				echo '<input type="text" name="nectar_meta['. $field['id'] .']" id="'. $field['id'] .'" value="'. ($meta ? $meta : $field['std']) .'" size="30"  />';
				echo '</td>';
				break;
			case 'slider_button_textarea':
				if($field['extra'] == 'first'){
					$count++;
					echo '<tr><td><label><strong>Button #'.$count.'</strong> <span>Configure your button here.</span> </label></td>';
				}
				echo '<td class="inline">';
				if($inline != null) {
					echo '<label for="'. $field['id'] .'"><strong>'. $field['name'] .'</strong>
			 		 <span>'. $field['desc'] .'</span></label>';
				}
				echo '<textarea name="nectar_meta['. $field['id'] .']" id="'. $field['id'] .'">'.($meta ? $meta : $field['std']) .'</textarea>';
				echo '</td>';
				break;
				
			case 'slider_button_select':
				echo '<td class="inline">';
				if($inline != null) {
					echo '<label for="'. $field['id'] .'"><strong>'. $field['name'] .'</strong>
			 		 <span>'. $field['desc'] .'</span></label>';
				}
				echo'<select name="nectar_meta['. $field['id'] .']" id="'. $field['id'] .'">';
				foreach( $field['options'] as $key => $option ){
					echo '<option value="' . $key . '"';
					if( $meta ){ 
						if( $meta == $key ) echo ' selected="selected"'; 
					} else {
						if( $field['std'] == $key ) echo ' selected="selected"'; 
					}
					echo'>'. $option .'</option>';
				}
				echo'</select></td>';
				if($field['extra'] == 'last'){
					echo '</tr>';
				}
				break;
			case 'checkbox':
			    echo '<td>';
			    $val = '';
                if( $meta ) {
                    if( $meta == 'on' ) $val = ' checked="checked"';
                } else {
                    if( $field['std'] == 'on' ) $val = ' checked="checked"';
                }

                echo '<input type="hidden" name="nectar_meta['. $field['id'] .']" value="off" />
                <input type="checkbox" id="'. $field['id'] .'" name="nectar_meta['. $field['id'] .']" value="on"'. $val .' /> ';
			    echo '</td>';
			    break;
			case 'caption_pos' :
				
				wp_register_style(
	                'redux-opts-jquery-ui-custom-css',
	                apply_filters('redux-opts-ui-theme',  NECTAR_FRAMEWORK_DIRECTORY . 'options/css/custom-theme/jquery-ui-1.10.0.custom.css'),
	                '',
	                time(),
	                'all'
	            );
				 wp_enqueue_style('redux-opts-jquery-ui-custom-css');
		         wp_enqueue_script(
		            'redux-opts-field-button_set-js', 
		            NECTAR_FRAMEWORK_DIRECTORY . 'options/fields/button_set/field_button_set.js', 
		            array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'),
		            time(),
		            true
		        );
				if($field['extra'] == 'first'){
					echo '<tr><td><label><strong>Slide Content Alignment</strong> <span>Configure the position for your slides content</span> </label></td>';
				}
				echo '<td class="content-alignment"> <label><strong>'.$field['desc'].'</strong><span>Select Your Alignment</span></label>';
				    echo '<fieldset class="buttonset">';
						foreach( $field['options'] as $key => $option ){
				
							echo '<input type="radio" id="nectar_meta_'. $key .'" name="nectar_meta['. $field["id"] .']" value="'. $key .'" ';
							if( $meta ){ 
								if( $meta == $key ) echo ' checked="checked"'; 
							} else {
								if( $field['std'] == $key ) echo ' checked="checked"';
							}
							echo ' /> ';
							echo '<label for="nectar_meta_'. $key .'"> '.$option.'</label>';
							
						}
					echo '</fieldset>';
				echo '</td>';
				if($field['extra'] == 'last'){
					echo '</tr>';
				}
				break;
		}
		
	
		
		if($inline == null) {
			echo '</tr>';
		}
	}
 
	echo '</table>';
}


#-----------------------------------------------------------------#
# Save Meta
#-----------------------------------------------------------------#

function nectar_save_meta_box( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;
	
	if ( !isset($_POST['nectar_meta']) || !isset($_POST['nectar_meta_box_nonce']) || !wp_verify_nonce( $_POST['nectar_meta_box_nonce'], basename( __FILE__ ) ) )
		return;
	
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) ) return;
	} 
	else {
		if ( !current_user_can( 'edit_post', $post_id ) ) return;
	}
 
	foreach( $_POST['nectar_meta'] as $key=>$val ){
		update_post_meta( $post_id, $key, $val );
	}

}

add_action( 'save_post', 'nectar_save_meta_box' );





