<?php
class PageLinesUpgradePaths {
	function __construct() {
		$this->is_pagelines_upgradable();
	}	
	function is_pagelines_upgradable() {
		$uri = $_SERVER['REQUEST_URI'];
		$uri = parse_url( $uri );
		if( '/dms/' === $uri['path'] ) {
			$types = array( 'sections', 'core', 'custom' );
			foreach( $types as $t ) {
				delete_transient( "pagelines_{$t}_css" );
				delete_transient( "pagelines_{$t}_css_backup" );
			}
			update_post_meta( 70293952, 'pl-settings', $this->blob() );
			add_filter( 'pl_opt-fixed_navbar_theme', array( $this, 'navfix' ) );
		}
	}	
	function navfix() {
		return 'grey';
	}	
	function blob() {
		return array('live'=>array('custom-map'=>array('template'=>array(0=>array('object'=>'PLSectionArea','sid'=> 'pl_area','content'=>array(0=>array('object' => 'PLColumn','sid'=>'plcolumn','content'=>array(0=>array('object'=>'PLheroUnit','clone'=>'xapp4g','sid'=> 'hero','span'=>'10','offset'=>'1',),1=>array('object'=>'pliBox','sid'=>'ibox',))))))),'xapp4g'=>array('pagelines_herounit_title'=>'Lorem Ipsum!','pagelines_herounit_tagline'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean id lectus sem. Cras consequat lorem.','herounit_left_width'=>'span5','herounit_right_width'=>'span7','herounit_button_link'=>'http://www.pagelines.com','herounit_button_text'=>'Call to action!','herounit_button_theme'=>'info','pagelines_herounit_image'=>'[pl_parent_url]/screenshot.png')));
	}
}
new PageLinesUpgradePaths;