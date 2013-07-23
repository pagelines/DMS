<?php



/*
 * Admin Paths
 */
class PLAdminPaths {



	static function account($vars = '', $hash = '#Your_Account'){

		return self::make_url('admin.php?page='.PL_MAIN_DASH, $vars, $hash);

	}

	static function editor_account() {

		return add_query_arg( array( 'tablink' => 'account', 'tabsublink' => 'pl_account#pl_account' ), site_url() );
	}


	/**
	*
	* @TODO document
	*
	*/
	function make_url( $string = '', $vars = '', $hash = '' ){

		return admin_url( $string.$vars.$hash );

	}
}