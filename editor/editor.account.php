<?php


class PLAccountPanel{

	function __construct(){

		if( pl_use_editor() )
			add_filter( 'pl_toolbar_config', array( &$this, 'toolbar' ) );

		add_action( 'wp_ajax_pl_account_actions', array( &$this, 'pl_account_actions' ) );
		add_action( 'admin_init', array( $this, 'activation_check_function' ) );
	}

	function activation_check_function() {

		$check = false;

		if( defined( 'DOING_AJAX' ) && true == DOING_AJAX )
			return;

		if ( ! current_user_can( 'edit_theme_options' ) )
			return;

		if( ! pl_is_pro() ) // no need if were not activated
			return;

		$data = get_option( 'dms_activation', array( 'active' => false, 'key' => '', 'message' => '', 'email' => '' ) );

		if( ! isset( $data['date'] ) ) {
			$data['date'] = date( 'Y-m-d' );
		}

		if( $data['date'] <= date( 'Y-m-d' ) )
			$check = true;

		if( false == $check )
			return;

		$url = sprintf( 'http://www.pagelines.com/index.php?wc-api=software-api&request=%s&product_id=dmspro&licence_key=%s&email=%s&instance=%s', 'check', $data['key'], $data['email'], site_url() );


		$result = wp_remote_get( $url );

		// if wp_error save error and abort.
		if( is_wp_error($result) ) {
			$data['last_error'] = $result->get_error_message();
			update_option( 'dms_activation', $data );
			return false;
		} else {
			$data['last_error'] = '';
			update_option( 'dms_activation', $data );
		}

		// do a couple of sanity checks..
		if( ! is_array( $result ) )
			return false;

		if( ! isset( $result['body'] ) )
			return false;

		$rsp = json_decode( $result['body'] );

		if( ! is_object( $rsp ) )
			return false;

		if( ! isset( $rsp->success ) )
			return false;

		// if success is true means the key was valid, move along nothing to see here.
		if( true == $rsp->success ) {

			$data['date'] = date('Y-m-d', strtotime('+7 days', strtotime( $data['date'] ) ) );
			update_option( 'dms_activation', $data );

			return;
		}

		if( isset( $rsp->error ) && isset( $rsp->code ) ) {
			// lets try again tomorrow
			$data['date'] = date('Y-m-d', strtotime('+1 days', strtotime( $data['date'] ) ) );
			$data['trys'] = ( isset( $data['trys'] ) ) ? $data['trys'] + 1 : 1;
			update_option( 'dms_activation', $data );

			if( $data['trys'] < 3 ) // try 2 times.
				return;

			self::send_email( $rsp, $data );
		}
	}

	function send_email( $rsp, $data ) {

			$data = get_option( 'dms_activation' );
			$message = sprintf( "The DMS activation key %s failed to authenticate after 2 tries. Please log into your account and check your subscription at https://www.pagelines.com/my-account/\n\nThe keyserver error was: %s", $data['key'], $rsp->error );
			wp_mail( get_bloginfo( 'admin_email' ), 'DMS Activation Failed', $message );
			update_option( 'dms_activation', array() );
	}

	function pl_account_actions() {
		$postdata = $_POST;
		$response = array();

		$response['key'] = $postdata['key'];
		$response['email'] = $postdata['email'];
		$response['active'] = false;
		$response['refresh'] = false;

		$activated = array( 'active' => false, 'key' => '', 'message' => '', 'email' => '' );

		if( $postdata['key'] && $postdata['email'] ) {
			$state = 'activation';

			if( isset( $postdata['revoke'] ) && true == $postdata['revoke'] )
				$state = 'deactivation';

			$url = sprintf( 'http://www.pagelines.com/?wc-api=software-api&request=%s&product_id=dmspro&licence_key=%s&email=%s&instance=%s', $state, $response['key'], $response['email'], site_url() );

			$response['url'] = $url;

			$data = wp_remote_get( $url );

			$rsp = json_decode( $data['body'] );

			if( isset( $rsp->activated ) ) {
				$response['active'] = $rsp->activated;
			}
			$message = ( isset( $rsp->message ) ) ? $rsp->message : '';
			$response['message'] = ( isset( $rsp->error ) ) ? $rsp->error : $message;

			} else {
				$response['message'] = 'There was an error!';
			}
		if( isset( $rsp->activated ) && true == $rsp->activated ) {
			$activated['message'] = $rsp->message;
			$activated['instance'] = $rsp->instance;
			$activated['active'] = true;
			$activated['key'] = $response['key'];
			$activated['email'] = $response['email'];
			$activated['date'] = date( 'Y-m-d' );
			$response['refresh'] = true;
		}

		if( isset( $rsp->reset ) && true == $rsp->reset ){
			$response['message'] = 'Deactivated key for ' . site_url();
			$response['refresh'] = true;
		}

	//	$response['rsp'] = $rsp;
		update_option( 'dms_activation', $activated );
		echo json_encode(  pl_arrays_to_objects( $response ) );

		exit();
	}

	function toolbar( $toolbar ){
		$toolbar['account'] = array(
			'name'	=> 'Account',
			'icon'	=> 'icon-pagelines',
			'pos'	=> 110,
		//	'type'	=> 'btn',
			'panel'	=> array(
				'heading'	=> "<i class='icon-pagelines'></i> PageLines",
				'welcome'	=> array(
					'name'	=> 'Welcome!',
					'icon'	=> 'icon-star',
					'call'	=> array(&$this, 'pagelines_welcome'),
				),
				'pl_account'	=> array(
					'name'	=> 'Your Account',
					'icon'	=> 'icon-user',
					'call'	=> array(&$this, 'pagelines_account'),
				),
				'support'	=> array(
					'name'	=> 'Support',
					'icon'	=> 'icon-comments',
					'call'	=> array(&$this, 'pagelines_support'),
				),
			)
		);

		return $toolbar;
	}

	function pagelines_welcome(){
		?>

		<h3><i class="icon-pagelines"></i> Congrats! You're using PageLines DMS.</h3>
		<p>
			Welcome to PageLines DMS, the world's first comprehensive drag and drop design management system.<br/>
			You've made it this far, now let's take a minute to show you around. <br/>
			<a href="#" class="dms-tab-link btn btn-success btn-mini" data-tab-link="account" data-stab-link="pl_account"><i class="icon-user"></i> Add Account Info</a>

		</p>
		<p>
			<iframe width="560" height="315" src="//www.youtube.com/embed/BracDuhEHls?rel=0&vq=hd720" frameborder="0" allowfullscreen></iframe>
		</p>

		<?php
	}

	function pagelines_account(){

		$disabled = '';
		$email = '';
		$key = '';
		$activate_text = '<i class="icon-ok"></i> Activate';
		$activate_btn_class = 'btn-primary'; 
		if( pl_is_pro() ) {
			$disabled = ' disabled';
			$data = get_option( 'dms_activation' );
			$email = sprintf( 'value="%s"', $data['email'] );
			$key = sprintf( 'value="%s"', $data['key'] );
			printf( '<div class="account-description"><div class="alert alert-info">%s</div></div>', $data['message'] );
			$activate_text = '<i class="icon-remove"></i> Deactivate';
			$activate_btn_class = 'btn-important'; 
		}

		if( ! pl_is_pro() ){ ?>
			<h3><i class="icon-key"></i> Enter your DMS Pro Activation key</h3>
			<p class="account-description">
				If you are a Pro member, activate to unlock pro sections, tools, libraries and support.
			</p>
		<?php } else { ?>
			<h3><i class="icon-key"></i> You're A Pro!</h3>
			<p class="account-description">
				Congratulations! The latest and greatest DMS tools and features are activated. 
			</p>
		<?php 
		
		}
		?>
		<label for="pl_activation">User email</label>
		<input type="text" class="pl-text-input" name="pl_email" id="pl_email" <?php echo $email . $disabled ?> />

		<label for="pl_activation">Activation key</label>
		<input type="text" class="pl-text-input" name="pl_activation" id="pl_activation" <?php echo $key . $disabled ?>/>


		<?php
		if( pl_is_pro() ) {
			echo '<input type="hidden" name="pl_revoke" id="pl_revoke" value="true" />';
		}

		?>
		<div class="submit-area">
			<button class="btn <?php echo $activate_btn_class;?> settings-action" data-action="pagelines-account"><?php echo $activate_text; ?></button>
		</div>
		<?php

	}

	function pagelines_support(){
		?>
		<h3><i class="icon-thumbs-up"></i> The PageLines Experience</h3>
		<p>
			We want you to have a most amazing time as a PageLines customer. <br/>
			That's why we have a ton of people standing by to make you happy.
		</p>
		<p>
			<a href="http://www.pagelines.com/forum" class="btn" target="_blank"><i class="icon-comments"></i> PageLines Forum</a>
			<a href="http://docs.pagelines.com" class="btn" target="_blank"><i class="icon-file"></i> DMS Documentation</a>
		</p>

		<?php
	}
}