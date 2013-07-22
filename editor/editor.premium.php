<?php 


function pl_is_pro(){	

	$status = get_option( 'dms_activation', array( 'active' => false, 'key' => '', 'message' => '', 'email' => '' ) );
	
	$pro = (true === $status['active']) ? true : false;
	
	return $pro;
	
}

function pl_pro_text(){
	
	return (!pl_is_pro()) ? __('(Pro Edition Only)', 'pagelines') : '';
	
}

function pl_pro_disable_class(){
	
	return (!pl_is_pro()) ? 'pro-only-disabled' : ''; 
	
}
