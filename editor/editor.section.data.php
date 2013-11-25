<?php 



class PLSectionData{
	
	var $version_slug = "pl_db_version";
	function __construct(){
		global $wpdb;
		$this->current_db_version = 0.2;
		$this->table_name = $wpdb->prefix . "pl_data_sections";
		$this->installed_db_version = get_option( $this->version_slug );
		
		// check if install needed, if so, run install routine
	
		if( $this->installed_db_version != $this->current_db_version )
			$this->install_table();
	}
	
	function install_table(){
		
		global $wpdb;

		$sql = "CREATE TABLE $this->table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				uid tinytext NOT NULL,
				settings text NOT NULL,
				live text NOT NULL,
				UNIQUE KEY id (id)
			);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		update_option( $this->version_slug, $this->current_db_version );
		
		$this->install_data();
	}
	
	function install_data() {
	   global $wpdb;

	   $rows_affected = $wpdb->insert( $this->table_name, array( 'uid'	=> 'u12345', 'settings' => '' ) );
	}
	
	function get_section_data(){
		
	}
	
}
