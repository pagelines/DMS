<?php


// switch up button class
add_filter('woocommerce_loop_add_to_cart_link', 'pl_commerce_switch_buttons');
function pl_commerce_switch_buttons( $button ){
	
	$button = str_replace('button', 'btn btn-mini', $button); 
	
	return $button;
	
}