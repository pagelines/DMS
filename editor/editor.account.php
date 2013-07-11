<?php


class PLAccountPanel{

	function __construct(){

		if( pl_use_editor() )
			add_filter( 'pl_toolbar_config', array( &$this, 'toolbar' ) );

		add_action( 'wp_ajax_pl_account_actions', array( &$this, 'pl_account_actions' ) );

		add_action( 'template_redirect', array( &$this, 'activation_check' ) );
	}

	function activation_check() {

		// If not activated we dont need to check for activation /0!
		if( ! pl_is_pro() )
			return;

		$data = get_option( 'dms_activation' );

		$url = sprintf( 'http://www.pagelines.com/?wc-api=software-api&request=%s&product_id=dmspro&licence_key=%s&email=%s&instance=%s', 'check', $data['key'], $data['email'], site_url() );

		$data = wp_remote_get( $url );

		// do a couple of sanity checks..
		if( ! isset( $data['body'] ) )
			return false;

		$rsp = json_decode( $data['body'] );

		if( ! is_object( $rsp ) )
			return false;

		if( ! isset( $rsp->success ) )
			return false;

		// if success is true means the key was valid, move along nothing to see here.
		if( true == $rsp->success )
			return;

		// Either the key is invalid or there was an error..

		if( isset( $rsp->error ) && isset( $rsp->code ) && 102 == $rsp->code)
			$this->send_email( $rsp->code, $data );

	}

	function send_email( $error, $data ) {

		if( 102 == $error ) {

			$message = sprintf( 'The key <strong>%s</strong> failed to authenticate.', $data['key'] );
			wp_mail( get_bloginfo( 'admin_email' ), 'Activation Failed', $message, $headers = "Content-Type: text/htmlrn", $attachments = "" );
			update_option( 'dms_activation', array( 'active' => false, 'key' => '', 'message' => '', 'email' => '' ) );
		}
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
			<a href="#" class="dms-tab-link btn btn-success btn-mini" data-tab-link="account" data-stab-link="account"><i class="icon-user"></i> Add Account Info</a>

		</p>
		<p>
			<iframe width="560" height="315" src="//www.youtube.com/embed/_EDemMLMcQ0" frameborder="0" allowfullscreen></iframe>
		</p>

		<?php
	}

	function pagelines_account(){

		$disabled = '';
		$email = '';
		$key = '';
		$activate_text = 'Activate';
		if( pl_is_pro() ) {
			$disabled = ' disabled';
			$data = get_option( 'dms_activation' );
			$email = sprintf( 'value="%s"', $data['email'] );
			$key = sprintf( 'value="%s"', $data['key'] );
			printf( '<div class="account-description"><div class="alert alert-info">%s</div></div>', $data['message'] );
			$activate_text = 'Deactivate';
		}

		if( ! pl_is_pro() ){
		?>
		<h3><i class="icon-user"></i> Enter your PageLines DMS Activation key</h3>
		<p class="account-description">
			If you are a Pro member, it will unlock pro features.
		</p>
		<?php }
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
			<button class="btn btn-primary settings-action" data-action="pagelines-account"><?php echo $activate_text; ?></button>
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