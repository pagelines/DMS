<?php



class EditorXList{

	function __construct(){

		add_action('pagelines_editor_scripts', array( $this, 'scripts'));

		$this->url = PL_PARENT_URL . '/editor';
	}

	function scripts(){

		// Isotope
		wp_enqueue_script( 'isotope', PL_JS . '/utils.isotope.min.js', array('jquery'), pl_get_cache_key(), true);

		wp_enqueue_script( 'pl-js-xlist', $this->url . '/js/pl.xlist.js', array('jquery'), pl_get_cache_key(), true);

	}

	function defaults(){
		$d = array(
			'id'			=> '',
			'class_array' 	=> array(),
			'data_array'	=> array(),
			'thumb'			=> '',
			'splash'		=> '',
			'name'			=> 'No Name',
			'sub'			=> false,
			'actions'		=> '',
			'format'		=> 'touchable',
			'icon'			=> ''
		);

		return $d;
	}

	function get_x_list_item( $args ){

		$args = wp_parse_args($args, $this->defaults());

		$classes = join(' ', $args['class_array']);

		$datas = '';
		foreach($args['data_array'] as $field => $val){
			$datas .= sprintf("data-%s='%s' ", $field, $val);
		}

		$sub = ($args['sub'] == 'full-width') ? 'icon-arrows-h' : 'icon-square-o';
		$sub_title = ($args['sub'] == 'full-width') ? 'Full Width Section' : 'Content Width Section';
		$drag_to = ($args['sub'] == 'full-width') ? 'Drag To Page as Full-Width Section' : 'Drag Into Canvas or Column Area';
		
		$xID = ($args['id'] != '') ? sprintf("data-extend-id='%s'", $args['id']) : '';
		
		$pad_class = 'x-item-els fix';

		$icon = sprintf('<div class="item-el"><i class="icon icon-check icon-2x"></i></div>');
		
		$elements = sprintf(
			'<div class="item-el el-icon tt-top" title="%s"><i class="icon icon-%s"></i></div>
			<div class="item-el el-name tt-top" title="%s">%s</div>
			<div class="item-el el-type tt-top" title="%s"><i class="icon %s"></i></div>
			<div class="item-el el-info tt-top" title="Documentation"><i class="icon icon-link"></i></div>',
			$drag_to,
			$args['icon'],
			$drag_to,
			$args['name'],
			$sub_title,
			$sub
		);

		$list_item = sprintf(
			"<section id='%s_%s' class='x-item x-extension %s %s' %s %s>
				<div class='x-item-pad'>
					<div class='x-item-els fix'>
						%s
					</div>
				</div>
			</section>",
			$args['id'],
			pl_new_clone_id(),
			'filter-'.$args['id'],
			$classes,
			$datas,
			$xID,
			$elements
		);

		return $list_item;

	}

	function get_action_out( $actions ){

		if(!empty($actions)){

			foreach($actions as $action){

				$action = wp_parse_args($action, $this->defaults());

				$action_classes = join(' ', $action['class_array']);

				$action_datas = '';
				foreach($action['data_array'] as $field => $val){
					$action_datas .= sprintf("data-%s='%s' ", $field, $val);
				}

				$action_name = $action['name'];

				$action_output .= sprintf('<a class="btn btn-mini %s" %s>%s</a> ', $action_classes, $action_datas, $action_name);

			}
			return sprintf('<div class="x-item-actions">%s</div>', $action_output);

		} else
			return '';



	}

}