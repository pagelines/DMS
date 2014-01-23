<?php

// all cache functions/actions

class DMS_Cache {
	
	function __construct() {
		add_action( 'activate_plugin', array( $this, 'run_plugin_theme_update' ) );
		add_action( 'deactivate_plugin', array( $this, 'run_plugin_theme_update' ) );
		add_action( 'upgrader_process_complete', array( $this, 'run_plugin_theme_update') );
		add_action( 'after_switch_theme', array( $this, 'run_plugin_theme_update' ) );
		add_action( 'init', array( $this, 'remote_cache_flush' ) );
	}
	
	// find out what needs clearing
	function purge( $area ) {
				
		switch( $area ) {
			case 'sections':
				$this->purge_sections_cache();
				break;

			case 'draft':
				$this->pl_flush_draft_caches();
				break;
			
			case 'live_css':
				do_action( 'extend_flush' );
				break;
				
			case 'less_vars':
				$this->clear_less_vars();
				break;
				
			case 'all':
				$this->purge_all();
				break;
		}
	}

	function run_plugin_theme_update() {
		$this->purge('all');		
	}

	function pl_flush_draft_caches() {

		$caches = array( 'draft_core_raw', 'draft_core_compiled', 'draft_sections_compiled' );
		foreach( $caches as $key ) {
			pl_cache_del( $key );
		}
		$file = sprintf( '%s%s', trailingslashit( pl_get_css_dir( 'path' ) ), 'editor-draft.css' ); 
		if( is_file( $file ) )
			unlink( $file );
	}

	function purge_sections_cache() {
		delete_transient( 'pagelines_sections_cache' );
		set_theme_mod( 'editor-sections-data', array() );
	}
	
	function purge_all() {
		$this->purge('sections');
		$this->purge('draft');
		$this->purge('live_css');
		$this->purge('less_vars');
		pagelines_reset_pl_cache_key();
	}
	
	function remote_cache_flush() {

		if( ! defined( 'PL_DEV' ) )
			return;

		if( ! isset( $_REQUEST['pl_purge'] ) )
			return false;

		$key = md5( get_bloginfo( 'admin_email' ) . site_url() );
		if( $key === $_REQUEST['pl_purge'] ) {
			$this->purge_all();
			wp_die( sprintf( '<h2>Caches Cleared</h2><a href="%s">Go back..</a>', site_url() ), 'PageLines DMS', array( 'response' => 200 ) );
		}
			
	}
	
	function clear_less_vars() {
		pl_cache_del( 'pagelines_less_vars' );
	}
}
global $dms_cache;
$dms_cache = new DMS_Cache;

function pl_get_cache_key() {

	if ( '' != get_theme_mod( 'pl_cache_key' ) ) {
		return get_theme_mod( 'pl_cache_key' );
	} else { 	
		return pagelines_reset_pl_cache_key();
	}
}

function pagelines_reset_pl_cache_key() {
	$key = substr(uniqid(), -6);
	set_theme_mod( 'pl_cache_key', $key );
	return $key;
}