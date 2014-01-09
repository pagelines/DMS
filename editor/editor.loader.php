<?php 


class PageLinesPageLoader{
	
	
	
	function __construct(){
		
		if( ! pl_draft_mode() )
			return;
		
		if( pl_less_dev() ){
			add_action('pagelines_head', array( $this, 'load_time_tracker_start')); 
			add_action('wp_footer', array( $this, 'load_time_tracker_stop'));
		}
		
		
		add_action('wp_footer', array( $this, 'loader_ready_script'), 20 );
		add_action('pagelines_head_last', array( $this, 'loader_inline_style') );
		add_action('pagelines_before_site', array( $this, 'loader_html') );
	}
	
	function load_time_tracker_start(){
		echo '<script>var start = new Date();</script>';
	}
	
	function load_time_tracker_stop(){
		echo '<script>jQuery(window).load(function() { console.log("editor load time (ms)"); console.log(new Date() - start); })</script>';
	}
	
	function loader_ready_script(){
		?>
		<script>
			jQuery( document ).ready(function() {
				jQuery(".pl-loader").fadeOut(1000)
			})
		</script>
		<?php
	}
	
	function loader_inline_style(){
		?>
		<style>

				.no-js .pl-loader { display: none;  }
				body{margin: 0;}
				.pl-loader { display: block; position: fixed; top: 0; width: 100%; height: 100%; background: #fff; z-index: 100000; text-align: center;}
				.pl-loader, 
				.pl-loader p{
					font-family: 'Open Sans',helvetica, arial, sans-serif; 
					color: #CCC !important; 
				}
				.pl-loader a{
					color: rgba(66, 133, 243,.8) !important;
				}
				.pl-spinner {
				   height:60px;
				   width:60px;
				   margin:0 auto;
				   position:relative;
				   -webkit-animation: pl-rotation .6s infinite linear;
				   border:6px solid rgba(66, 133, 243,.25);
				   border-radius:100%;
				}

				.pl-spinner:before {
				   content:"";
				   display:block;
				   position:absolute;
				   left:-6px;
				   top:-6px;
				   height:100%;
				   width:100%;
				   border-top:6px solid rgba(66, 133, 243,.9);
				   border-left:6px solid transparent;
				   border-bottom:6px solid transparent;
				   border-right:6px solid transparent;
				   border-radius:100%;
				}

				@-webkit-keyframes pl-rotation {
				   from {-webkit-transform: rotate(0deg);}
				   to {-webkit-transform: rotate(359deg);}
				}
				
				
			</style>
			
		<?php
	}

	function loader_html(){
		
		
		?>
		<div class="pl-loader pl-pro-version">
			<div class="loader-text" style="padding: 200px 0;font-family: 'Open Sans', helvetica, arial, sans-serif; font-size: 30px; line-height: 1.9em; font-weight: 600; letter-spacing: -1px; color: #000;">
				<div class="pl-spin-c pl-animation pla-from-top "><div class="pl-spinner"></div></div>
				<div class="pl-loader-head pl-animation pla-from-bottom "><?php _e('Loading DMS Editor', 'pagelines');?></div>
				
				<script> 
				setTimeout(function() { jQuery(".pl-loader .pl-animation").addClass('animation-loaded') }, 150);
				
				setTimeout(function() { jQuery(".pl-loader .pl-loader-head").html('Oops, There may be an issue loading. <div style="font-size: 16px; opacity: .5; line-height: 1.55em;">DMS typically loads in less than 5 seconds. Please check for Javascript or PHP errors.<br/>(Typically, slow or incomplete loading is related to plugin conflicts or server issues.)<br/><a href="http://docs.pagelines.com/support-troubleshooting/common-issues" target="_blank">View the Troubleshooting Guide</a></div>') }, 13000);
				
				</script>
			</div>
		</div>
		
		<?php
		
	}
}
