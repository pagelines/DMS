<?php
/**
 * This file initializes the PageLines framework
 *
 * @package PageLines DMS
 *
*/

/**
 * Run the starting hook
 */
do_action('pagelines_hook_pre', 'core'); // Hook

define('PL_INCLUDES', get_template_directory() . '/includes');

if ( is_file( PL_INCLUDES . '/library.pagelines.php' ) )
	require_once( PL_INCLUDES . '/library.pagelines.php');


// Load deprecated functions
require_once( PL_INCLUDES.'/deprecated.php' );

// Run version checks and setup
require_once( PL_INCLUDES . '/run.versioning.php');

// Setup Globals
require_once( PL_INCLUDES . '/init.globals.php');

// LOCALIZATION - Needs to come after config_theme and before localized config files
require_once( PL_INCLUDES . '/run.I18n.php');

// Utility functions and hooks/filters
require_once( PL_INCLUDES . '/lib.utils.php' );

// Applied on load
require_once( PL_INCLUDES . '/lib.load.php' );

// Applied on load
require_once( PL_INCLUDES . '/lib.elements.php' );

// Applied in head
require_once( PL_INCLUDES . '/lib.head.php' );

// Applied in body
require_once( PL_INCLUDES . '/lib.body.php' );

/**
 * Editor
 */
require_once( PL_EDITOR . '/editor.init.php' );

// V3 Editor functions --- > always load
require_once( PL_EDITOR . '/editor.functions.php' );

/**
 * Load Options Functions
 */
require_once( PL_INCLUDES . '/library.options.php' );


/**
 * Load shortcode library
 */
require_once( PL_INCLUDES . '/class.shortcodes.php');

/**
 * Load Extension library
 */
require_once( PL_INCLUDES . '/library.extend.php');

/**
 * Load Layouts library
 */
require_once( PL_INCLUDES . '/library.layouts.php');


/**
 * Load Custom Post Type Class
 */
require_once( PL_INCLUDES . '/class.types.php' );

/**
 * Posts Handling
 */
require_once( PL_INCLUDES . '/class.posts.php' );


/**
 * Load layout class and setup layout singleton
 * @global object $pagelines_layout
 */
require_once( PL_INCLUDES . '/class.layout.php' );

require_once( PL_INCLUDES . '/library.layout.php' );


/**
 * Load sections handling class
 */
require_once( PL_INCLUDES . '/class.sections.php' );

/**
 * Load template handling class
 */
require_once( PL_INCLUDES . '/class.template.php' );

/**
 * Load Data Handling
 */
require_once( PL_ADMIN . '/library.data.php' );

/**
 * Load HTML Objects
 */
require_once( PL_INCLUDES . '/class.objects.php' );


/**
 * Load Type Foundry Class
 */
require_once( PL_INCLUDES . '/class.typography.php' );

/**
 * Load Colors
 */
require_once( PL_INCLUDES . '/class.colors.php' );

/**
 * Load dynamic CSS handling
 */
require_once( PL_INCLUDES . '/class.css.php' );

/**
 * Load metapanel option handling class
 */
require_once( PL_ADMIN . '/class.options.metapanel.php' );

/**
 * Load Profile Handling
 */
require_once( PL_ADMIN . '/class.profiles.php' );

/**
 * Deal with upgrading
 */
include( PL_INCLUDES . '/library.upgrades.php' );

/**
 * Add Integration Functionality
 */
require_once( PL_INCLUDES . '/class.integration.php' );

/**
 * Add Multisite
 */
if( is_multisite() )
	require_once( PL_INCLUDES . '/library.multisite.php' );

/**
 * Add Integration Functionality
 */
require_once( PL_INCLUDES . '/class.themesupport.php' );
/**
 * Add Less Functions
 */
require_once( PL_INCLUDES . '/less.functions.php' );

/**
 * Build Version
 */
require_once( PL_INCLUDES . '/version.php' );

require_once( PL_INCLUDES . '/class.render.css.php' );


/**
 * Load updater class
 */
require_once (PL_ADMIN.'/class.updates.php');

if ( is_admin() )
	new PageLinesUpdateCheck( PL_CORE_VERSION );


/**
 * Load site actions
 */
require_once (PL_INCLUDES.'/actions.site.php');
	
/**
 * Run the pagelines_init Hook
 */
pagelines_register_hook('pagelines_hook_init'); // Hook

// Always best to load most stuff after WP loads fully.
// The "after_setup_theme" hook is the point at which it has... 
// NOTE: pl_setting cannot be used BEFORE the 'after_setup_theme' hook
add_action('after_setup_theme', 'pl_load_registers'); 
function pl_load_registers(){

	/**
	 * Load Singleton Globals
	 */
	$GLOBALS['pl_section_factory'] = new PageLinesSectionFactory();


	/**
	 * Add Extension Handlers
	 */
	require_once( PL_INCLUDES . '/class.register.php' );

	/**
	 * Register and load all sections
	 */
	global $load_sections;
	$load_sections = new PageLinesRegister();
	$load_sections->pagelines_register_sections();

	pagelines_register_hook('pagelines_setup'); // Hook

	load_section_persistent(); // Load persistent section functions (e.g. custom post types)

	if(is_admin())
		load_section_admin(); // Load admin only functions from sections

	do_global_meta_options(); // Load the global meta settings tab


	$GLOBALS['render_css'] = new PageLinesRenderCSS;
	

	if ( pl_setting( 'enable_debug' ) )
		require_once ( PL_ADMIN . '/class.debug.php');



	if ( is_admin() )
		include( PL_ADMIN . '/init.admin.php' );


}



