<?php



class EditorCode{

	function __construct( ){

		add_filter( 'pl_toolbar_config',		array(&$this, 'toolbar'));
		
		add_action( 'pagelines_editor_scripts',	array(&$this, 'scripts'));
		add_action( 'pagelines_head_last',		array(&$this, 'draw_custom_scripts' ) );

		$this->url = PL_PARENT_URL . '/editor';
	}
	
	function scripts(){

		// Codemirror Styles
		wp_enqueue_style( 'codemirror',			PL_ADMIN_JS . '/codemirror/codemirror.css' );
		wp_enqueue_style( 'css3colorpicker',	$this->url . '/js/colorpicker/colorpicker.css');

		// CodeMirror Syntax Highlighting
		wp_enqueue_script( 'codemirror',		PL_ADMIN_JS . '/codemirror/codemirror.js', array( 'jquery' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'codemirror-css',	PL_ADMIN_JS . '/codemirror/css/css.js', array( 'jquery', 'codemirror' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'codemirror-less',	PL_ADMIN_JS . '/codemirror/less/less.js', array( 'jquery', 'codemirror' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'codemirror-js',		PL_ADMIN_JS . '/codemirror/javascript/javascript.js', array( 'jquery', 'codemirror' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'codemirror-xml',	PL_ADMIN_JS . '/codemirror/xml/xml.js', array( 'jquery', 'codemirror' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'codemirror-html',	PL_ADMIN_JS . '/codemirror/htmlmixed/htmlmixed.js', array( 'jquery', 'codemirror' ), PL_CORE_VERSION, true );

		// PageLines Specific JS @Code Stuff
		wp_enqueue_script( 'pl-less-parser',	$this->url . '/js/utils.less.js', array( 'jquery' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'pl-js-code',		$this->url . '/js/pl.code.js', array( 'jquery', 'codemirror', 'pl-less-parser' ), PL_CORE_VERSION, true );
		
		// less.js
		$lessjs_config = array('env' => is_pl_debug() ? 'development' : 'production');
		wp_localize_script( 'pl-less-parser', 'less', $lessjs_config );

		// Codebox defaults
		$base_editor_config = array(
			'lineNumbers'  => true,
			'lineWrapping' => true,
		);
		wp_localize_script( 'codemirror', 'cm_base_config', apply_filters( 'pagelines_cm_config', $base_editor_config ) );
	}

	function toolbar( $toolbar ){
		$toolbar['pl-design'] = array(
				'name'	=> __( 'Custom Code', 'pagelines' ),
				'icon'	=> 'icon-code',
				'form'	=> true,
				'pos'	=> 40,
				'panel'	=> array(
					'heading'	=> __( 'Custom Design', 'pagelines' ),

					'user_less'	=> array(
						'name'	=> __( 'Custom LESS/CSS', 'pagelines' ),
						'call'	=> array(&$this, 'custom_less'),
						'icon'	=> 'icon-circle'
					),
					'user_scripts'	=> array(
						'name'	=> __( 'Custom Scripts', 'pagelines' ),
						'call'	=> array(&$this, 'custom_scripts'),
						'flag'	=> 'custom-scripts',
						'icon'	=> 'icon-circle-blank'
					),
				)
			);

		return $toolbar;
	}

	function draw_custom_scripts(){
		echo stripslashes( pl_setting('custom_scripts') );
	}


	function custom_less(){
		?>
		<div class="opt codetext">
			<div class="opt-name">
				<?php _e( 'Custom LESS/CSS', 'pagelines' ); ?>
			</div>
			<div class="opt-box">
				<div class="codetext-meta fix">
					<label class="codetext-label"><?php _e( 'Custom LESS/CSS', 'pagelines' ); ?></label>
					<span class="codetext-help help-block"><span class="label label-info"><?php _e( 'Tip', 'pagelines' ); ?></span> <?php _e( 'Hit [Cmd&#8984;+Return] or [Ctrl+Return] to Preview Live', 'pagelines' ); ?></span>
				</div>
				<form class="code-form"><textarea id="custom_less" class="custom-less" name="settings[custom_less]" placeholder=""><?php echo pl_setting('custom_less'); ?></textarea></form>
			</div>
		</div>
		<?php
	}

	function custom_scripts(){
		?>
		<div class="opt codetext">
			<div class="opt-name">
				<?php _e( 'Custom Scripts', 'pagelines' ); ?>
			</div>
			<div class="opt-box">
				<div class="codetext-meta fix">
					<label class="codetext-label"><?php _e( 'Custom Javascript or Header HTML', 'pagelines' ); ?></label>
				</div>
				<form class="code-form"><textarea id="custom_scripts" class="custom-scripts" name="settings[custom_scripts]" placeholder=""><?php echo stripslashes( pl_setting( 'custom_scripts' ) ); ?></textarea></form>
			</div>
		</div>
		<?php
	}
}