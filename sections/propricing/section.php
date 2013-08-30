<?php
/*
	Section: ProPricing
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: An amazing, professional pricing section.
	Class Name: PLProPricing
	Filter: component
	Loading: active
	Edition: pro
*/


class PLProPricing extends PageLinesSection {

	var $default_limit = 3;

	function section_styles(){
		
	}


	function section_opts(){
		$options = array();

		$options[] = array(

			'title' => __( 'ProPricing Configuration', 'pagelines' ),
			'type'	=> 'multi',
			'opts'	=> array(
				array(
					'key'			=> 'propricing_count',
					'type' 			=> 'count_select',
					'count_start'	=> 1,
					'count_number'	=> 12,
					'default'		=> 3,
					'label' 	=> __( 'Number of Plans to Configure', 'pagelines' ),
				),
				array(
					'key'			=> 'propricing_cols',
					'type' 			=> 'count_select',
					'count_start'	=> 1,
					'count_number'	=> 12,
					'default'		=> '4',
					'label' 	=> __( 'Number of Columns for Each Plan (12 Col Grid)', 'pagelines' ),
				),
			)

		);

		$slides = ($this->opt('propricing_count')) ? $this->opt('propricing_count') : $this->default_limit;
	
		for($i = 1; $i <= $slides; $i++){

			$opts = array(

				array(
					'key'		=> 'propricing_title_'.$i,
					'label'		=> __( 'ProPricing Title', 'pagelines' ),
					'type'		=> 'text'
				),
				array(
					'key'		=> 'propricing_price_'.$i,
					'label'	=> __( 'ProPricing Text', 'pagelines' ),
					'type'	=> 'text'
				),
				array(
					'key'		=> 'propricing_price_pre_'.$i,
					'label'	=> __( 'ProPricing Before Price Text', 'pagelines' ),
					'type'	=> 'text',
					'help'	=> __( 'Typically you will add the monetary unit here. E.g. "$"', 'pagelines' ),
				),
				array(
					'key'		=> 'propricing_price_post_'.$i,
					'label'	=> __( 'ProPricing After Price Text', 'pagelines' ),
					'type'	=> 'text',
					'help'	=> __( 'Typically you will add the recurring amount here. E.g. "/ MO"', 'pagelines' ),
				),
				array(
					'key'		=> 'propricing_sub_'.$i,
					'label'	=> __( 'ProPricing Sub Text', 'pagelines' ),
					'type'	=> 'text'
				),
				array(
					'key'		=> 'propricing_link_'.$i,
					'label'	=> __( 'ProPricing Link URL', 'pagelines' ),
					'type'	=> 'text'
				),
				array(
					'key'		=> 'propricing_link_text_'.$i,
					'label'	=> __( 'ProPricing Link Text', 'pagelines' ),
					'type'	=> 'text'
				),
				array(
					'key'		=> 'propricing_btn_'.$i,
					'label'	=> __( 'ProPricing Button Theme', 'pagelines' ),
					'type'	=> 'select_button'
				),
				array(
					'key'		=> 'propricing_attributes_'.$i,
					'label'	=> __( 'ProPricing Attributes', 'pagelines' ),
					'type'	=> 'textarea',
					'help'	=> __( 'Add each attribute on a new line. Add a "*" in front to add emphasis.', 'pagelines' ),
				),
			);


			$options[] = array(
				'title' 	=> __( 'ProPricing Plan ', 'pagelines' ) . $i,
				'type' 		=> 'multi',
				'opts' 		=> $opts,

			);

		}

		return $options;
	}


   function section_template( ) { 
	
		$cols = ($this->opt('propricing_cols')) ? $this->opt('propricing_cols') : 4;
		$num = ($this->opt('propricing_count')) ? $this->opt('propricing_count') : $this->default_limit;
		$width = 0;
		$output = '';
	
		$master = array();
		for($i = 1; $i <= $num; $i++){
			
			$master[$i]['title'] = ($this->opt('propricing_title_'.$i)) ? $this->opt('propricing_title_'.$i) : 'Plan'; 
			$master[$i]['price'] = ($this->opt('propricing_price_'.$i)) ? $this->opt('propricing_price_'.$i) : $i*8; 
			$master[$i]['price_pre'] = ($this->opt('propricing_price_pre_'.$i)) ? $this->opt('propricing_price_pre_'.$i) : '$'; 
			$master[$i]['price_post'] = ($this->opt('propricing_price_post_'.$i)) ? $this->opt('propricing_price_post_'.$i) : '/ MO'; 
			
			$master[$i]['sub'] = ($this->opt('propricing_sub_'.$i)) ? $this->opt('propricing_sub_'.$i) : sprintf('Billed annually or $%s/MO billed monthly.', $i*10); 
			$master[$i]['link'] = ($this->opt('propricing_link_'.$i)) ? $this->opt('propricing_link_'.$i) : 'http://www.pagelines.com/pricing'; 
			$master[$i]['link_text'] = ($this->opt('propricing_link_text_'.$i)) ? $this->opt('propricing_link_text_'.$i) : ''; 
			$master[$i]['btn_theme'] = ($this->opt('propricing_btn_'.$i)) ? $this->opt('propricing_btn_'.$i) : 'btn-important'; 
			
			$master[$i]['attr'] = ($this->opt('propricing_attributes_'.$i)) ? $this->opt('propricing_attributes_'.$i) : '';
			
		}

		foreach($master as $i => $plan){
			
			
			$title 		= $plan['title']; 
			$price_pre 	= $plan['price_pre']; 
			$price 		= $plan['price']; 
			$price_post = $plan['price_post']; 
			$sub 		= $plan['sub']; 
			$link 		= $plan['link']; 
			$link_text 	= $plan['link_text']; 
			$btn_theme 	= $plan['btn_theme']; 
			$attr 		= $plan['attr']; 
		
		
			$attr_list = ''; 
			
			if($attr != ''){
				
				$attr_array = explode("\n", $attr);
				
				foreach($attr_array as $at){
					
					if(strpos($at, '*') === 0){
						$at = str_replace('*', '', $at); 
						$attr_list .= sprintf('<li class="emphasis">%s</li>', $at); 
					} else {
						$attr_list .= sprintf('<li>%s</li>', $at); 
					}
					
				}
				
			} 
			
			if($link != ''){
				
				$link_text = ($link_text != '') ? $link_text : 'Sign Up';
				$link_text = sprintf('<span class="btn-link-text" data-sync="propricing_link_text_%s">%s</span>', $i, $link_text);
				
				$formatted_link = sprintf('<li class="pp-link"><a href="%s" class="btn btn-large %s" >%s <i class="icon-chevron-sign-right"></i></a></li>', $link, $btn_theme, $link_text);
				
			} else {
				$formatted_link = ''; 
			}
			
			
			$attr_list = $formatted_link . $attr_list; 
			
			$formatted_attr = ($attr_list != '') ? sprintf('<div class="pp-attributes"><ul>%s</ul></div>', $attr_list) : '';
		
		
			$formatted_sub = ($sub != '') ? sprintf('<div class="price-sub" data-sync="propricing_sub_%s">%s</div>', $i, $sub) : ''; 
		
			if($width == 0)
				$output .= '<div class="row fix">';

			$output .= sprintf(
				'<div class="span%1$s pp-plan pl-animation pl-appear fix">
					<div class="pp-header">
						<div class="pp-title" data-sync="propricing_title_%8$s">
							%2$s
						</div>
						<div class="pp-price">
							<span class="price-pre" data-sync="propricing_price_pre_%8$s">%3$s</span>
							<span class="price" data-sync="propricing_price_%8$s">%4$s</span>
							<span class="price-post" data-sync="propricing_price_post_%8$s">%5$s</span>
							%6$s
						</div>
					</div>
					%7$s
				</div>',
				$cols,
				$title,
				$price_pre, 
				$price,
				$price_post,
				$formatted_sub,
				$formatted_attr, 
				$i
			);

			$width += $cols;

			if($width >= 12 || $i == $num){
				$width = 0;
				$output .= '</div>';
			}


		 }
	
	
	?>
	
	<div class="propricing-wrap pl-animation-group">
		<?php echo $output; ?>
	</div>

<?php }


}
