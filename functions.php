<?php
/**
 * WP Rig functions and definitions
 *
 * This file must be parseable by PHP 5.2.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package wp_rig
 */

define( 'WP_RIG_MINIMUM_WP_VERSION', '5.4' );
define( 'WP_RIG_MINIMUM_PHP_VERSION', '8.0' );

// Bail if requirements are not met.
if ( version_compare( $GLOBALS['wp_version'], WP_RIG_MINIMUM_WP_VERSION, '<' ) || version_compare( phpversion(), WP_RIG_MINIMUM_PHP_VERSION, '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}

// Include WordPress shims.
require get_template_directory() . '/inc/wordpress-shims.php';

// Setup autoloader (via Composer or custom).
if ( file_exists( get_template_directory() . '/vendor/autoload.php' ) ) {
	require get_template_directory() . '/vendor/autoload.php';
} else {
	/**
	 * Custom autoloader function for theme classes.
	 *
	 * @access private
	 *
	 * @param string $class_name Class name to load.
	 * @return bool True if the class was loaded, false otherwise.
	 */
	function _wp_rig_autoload( $class_name ) {
		$namespace = 'WP_Rig\WP_Rig';

		if ( 0 !== strpos( $class_name, $namespace . '\\' ) ) {
			return false;
		}

		$parts = explode( '\\', substr( $class_name, strlen( $namespace . '\\' ) ) );

		$path = get_template_directory() . '/inc';
		foreach ( $parts as $part ) {
			$path .= '/' . $part;
		}
		$path .= '.php';

		if ( ! file_exists( $path ) ) {
			return false;
		}

		require_once $path;

		return true;
	}
	spl_autoload_register( '_wp_rig_autoload' );
}

// Load the `wp_rig()` entry point function.
require get_template_directory() . '/inc/functions.php';

// Add custom WP CLI commands.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once get_template_directory() . '/wp-cli/wp-rig-commands.php';
}

// Initialize the theme.
call_user_func( 'WP_Rig\WP_Rig\wp_rig' );

function dequeue_global_styles_on_author_page() {
    if ( is_author() || is_singular( 'gig' ) ) {

        wp_dequeue_style('wp-rig-global');
    }
}
add_action('wp_enqueue_scripts', 'dequeue_global_styles_on_author_page', 100);

// enqueue tailwind on author page
function wprig_enqueue_author_assets() {
    // Only enqueue the stylesheet on author pages.
    if ( is_author() || is_singular( 'gig' ) ) {
		// Build the URL to the CSS file.
		$css_url = get_template_directory_uri() . '/assets/css/vendor/tailwind-compiled.css';
	
		// Optionally, use file modification time as the version for cache busting.
		$css_path = get_template_directory() . '/assets/css/vendor/tailwind-compiled.css';
		$version = file_exists( $css_path ) ? filemtime( $css_path ) : false;
	
		wp_enqueue_style( 'wprig-tailwind', $css_url, array(), $version );
	}
	
}
add_action( 'wp_enqueue_scripts', 'wprig_enqueue_author_assets' );


// function log_enqueued_css_handles_and_files_to_error_log() {
//     global $wp_styles;

//     if (!isset($wp_styles) || empty($wp_styles->queue)) {
//         error_log('No CSS files are currently enqueued.');
//         return;
//     }

//     foreach ($wp_styles->queue as $handle) {
//         $style = $wp_styles->registered[$handle] ?? null;

//         if ($style && isset($style->src)) {
//             error_log('Enqueued CSS Handle: ' . $handle . ' | File: ' . esc_url($style->src));
//         } else {
//             error_log('Enqueued CSS Handle: ' . $handle . ' | File: Not Found');
//         }
//     }
// }
// add_action('wp_enqueue_scripts', 'log_enqueued_css_handles_and_files_to_error_log', 999);

