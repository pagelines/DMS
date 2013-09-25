<?php


class PLAccountPanel{

	function __construct(){

		if( pl_use_editor() )
			add_filter( 'pl_toolbar_config', array( &$this, 'toolbar' ) );		
	}
	
	function toolbar( $toolbar ){
		$toolbar['account'] = array(
			'name'	=> __( 'Welcome', 'pagelines' ),
			'icon'	=> 'icon-pagelines',
			'pos'	=> 110,
		//	'type'	=> 'btn',
			'panel'	=> array(
				'heading'	=> "<i class='icon-pagelines'></i> PageLines",
				'welcome'	=> array(
					'name'	=> __( 'Welcome!', 'pagelines' ),
					'icon'	=> 'icon-thumbs-up',
					'call'	=> array(&$this, 'pagelines_welcome'),
				),
				'getting_started'	=> array(
					'name'	=> __( 'Getting Started', 'pagelines' ),
					'icon'	=> 'icon-youtube-play',
					'call'	=> array($this, 'getting_started'),
				),				
				'support'	=> array(
					'name'	=> __( 'Support', 'pagelines' ),
					'icon'	=> 'icon-comments',
					'call'	=> array(&$this, 'pagelines_support'),
				),
			)
		);
		if( defined( 'DMS_DISABLE_ACCOUNT_PANEL' ) && DMS_DISABLE_ACCOUNT_PANEL && pl_is_pro() )
			unset( $toolbar['account']['panel']['pl_account']);
		if( defined( 'DMS_DISABLE_KARMA_PANEL' ) && DMS_DISABLE_KARMA_PANEL && pl_is_pro() )
			unset( $toolbar['account']['panel']['get_karma']);			
		return $toolbar;
	}
	

	
	function getting_started(){
		?>
		<p>
			<h3><i class="icon-thumbs-up"></i> <?php _e( 'Getting Started', 'pagelines' ); ?>
			</h3>
			<iframe width="700" height="420" src="//www.youtube.com/embed/BracDuhEHls?rel=0&vq=hd720" frameborder="0" allowfullscreen></iframe>
		</p>
		<?php 
	}

	function pagelines_welcome(){
		?>

		<h3><i class="icon-pagelines"></i> <?php _e( 'Congratulations!', 'pagelines' ); ?>
		</h3>
		<p>
		 	<strong>Hello! Welcome to DMS.</strong><br/> A drag <span class="spamp">&amp;</span> drop design management system for building, managing, and <em>evolving</em> your website.<br/> To get started please visit the links below &darr; 
		</p>
		<div class="alignleft well welcome-well">
			<?php if( defined( 'DMS_DISABLE_ACCOUNT_PANEL' ) && DMS_DISABLE_ACCOUNT_PANEL && pl_is_pro() ){} else { ?>
			<a href="#" class="dms-tab-link btn btn-primary" data-tab-link="account" data-stab-link="pl_account"><i class="icon-pagelines"></i> <?php _e( 'Setup PageLines Account', 'pagelines' ); ?>
			 <i class="icon-angle-right"></i></a>
			<?php } ?>
			<a href="#" class="dms-tab-link btn" data-tab-link="account" data-stab-link="getting_started"><i class="icon-youtube-play"></i> <?php _e( 'Getting Started Video', 'pagelines' ); ?>
			 <i class="icon-angle-right"></i></a>
		</div>
		

		<?php
	}
	

	function pagelines_support(){
		?>
		<h3><i class="icon-thumbs-up"></i> <?php _e( 'The PageLines Experience', 'pagelines' ); ?>
		</h3>
		<p>
			<?php _e( 'We want you to have a most amazing time as a PageLines customer.', 'pagelines' ); ?>
			 <br/>
			<?php _e( "That's why we have a ton of people standing by to make you happy.", 'pagelines' ); ?>
			
		</p>
		<p>
			<a href="http://forum.pagelines.com" class="btn" target="_blank"><i class="icon-comments"></i> <?php _e( 'PageLines Forum', 'pagelines' ); ?>
			</a>
			<a href="http://docs.pagelines.com" class="btn" target="_blank"><i class="icon-file"></i> <?php _e( 'DMS Documentation', 'pagelines' ); ?>
			</a>
		</p>

		<?php
	}
}

