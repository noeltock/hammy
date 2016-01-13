<?php
/*
Plugin Name: Hammy
Plugin URI: http://wordpress.org/extend/plugins/hammy/
Description: Creates adaptive images for your content area with breakpoints that you set.
Author: Noel Tock
Version: 1.5.0
Author URI: http://www.noeltock.com
*/

/**
 * Defines
 */
$path = explode( DIRECTORY_SEPARATOR, dirname( __FILE__ ) );
define ( 'HAMMY_VERSION', '1.5.1' );
define ( 'HAMMY_PATH',  WP_PLUGIN_URL . '/' . end( $path ) );

/**
 * Register Default Settings
 */
if ( ! get_option('hammy_options') ) {
    register_activation_hook( __FILE__, 'hammy_defaults' );
}

function hammy_defaults() {

	global $content_width;

	if ( $content_width ) {

		$breakpoints = '';
		if ( $content_width >= 400 ) $breakpoints .= '320,';
		if ( $content_width >= 600 ) $breakpoints .= '480,';
		$breakpoints .= $content_width;

	} else {

		$breakpoints = '320,480,624';

	}

    $arr = array( 'hammy_breakpoints' => $breakpoints, 'hammy_ignores' => 'nextgen, thumbnail', 'hammy_parent' => '.entry-content', 'hammy_lazy' => 'false');
    update_option( 'hammy_options', $arr );

}

/**
 * Check for WPThumb, include.
 * 
 */
if ( !function_exists( 'wpthumb' ) ) {
	include_once( __DIR__.'/WPThumb/wpthumb.php' );
}

/**
 * Plugin Options
 */
include_once( __DIR__.'/includes/options.php' );

add_action( 'admin_init', 'hammy_options_init' );
add_action( 'admin_menu', 'hammy_add_page' );

/**
 * Enqueue & Localize JavaScript & CSS ( TODO: find opportunity for minqueue)
 */
function load_hammy_js() {

	$options = get_option( 'hammy_options' );	
	
	if ( $options['hammy_lazy'] == 'true' ) {
		wp_enqueue_script( 'jquery-picture', HAMMY_PATH . '/js/jquery-picture-lazy.js', array( 'jquery' ), '1.5.1' , true );
		wp_enqueue_script( 'lazyload', HAMMY_PATH . '/js/jquery.lazyload.min.js', array( 'jquery' ), '1.5.1' , true );
		wp_enqueue_script( 'hammy', HAMMY_PATH . '/js/hammy-lazy.js', array( 'jquery' ), '1.5.1' , true  );
		wp_enqueue_style( 'hammy-stylesheet', HAMMY_PATH . '/css/hammy.css' );
	} else {
		wp_enqueue_script( 'jquery-picture', HAMMY_PATH . '/js/jquery-picture.js', array( 'jquery' ), '1.5.1' , true );
		wp_enqueue_script( 'hammy', HAMMY_PATH . '/js/hammy.js', array( 'jquery' ), '1.5.1' , true  );
		wp_enqueue_style( 'hammy-stylesheet', HAMMY_PATH . '/css/hammy.css' );
	}

    wp_localize_script( 'hammy', 'imageParent', $options['hammy_parent'] );
		
}

// TODO enqueue scripts

add_action( 'wp_print_scripts', 'load_hammy_js' );

function load_hammy_css() {

    wp_enqueue_style( 'hammy-css', HAMMY_PATH . '/css/hammy-admin.css' );

}

add_action( 'admin_print_styles', 'load_hammy_css' );

/**
 * Hammy Time
 *
 * @param $content
 * @return DOM element with fallback (<figure> -> <img>)
 */
function hammy_replace_images( $content ) {

	global $post;

    $options = get_option( 'hammy_options' );
	
	preg_match_all( '/<img (.*?)\/>/', $content, $images );
	
	if( !is_null( $images ) ) {
	
		foreach( $images[0] as $index => $value) {
		
			$doc = new DOMDocument();
			$doc->loadHTML( $value );
			$items = $doc->getElementsByTagName( 'img' );
				
			foreach( $items as $item ) {

				// Get Attributes
			
				$original = $item->getAttribute( 'src' );
				$width = $item->getAttribute( 'width' );
				$height = $item->getAttribute( 'height' );
				$class = $item->getAttribute( 'class' );
				$alt = $item->getAttribute( 'alt' );
				$title = $item->getAttribute( 'title' );

                // Check if it's part of an ignored class

                $ignoreClasses = explode( ",", $options['hammy_ignores'] );

                $ignorelist = '/' . implode( "|", $ignoreClasses ) . '/';

                if ( ! preg_match( $ignorelist, $class ) ) {
				
                    // Get Sizes

                    $sizes = explode( ",", $options['hammy_breakpoints'] );

                    // Render Sizes

                    $i = 0;
                    $breakpoint = null;

                    // Output & Replace Strings

                    $newimage = '<figure class="hammy-responsive ' . $class . '" title="' . $title . '" ';

                    foreach ( $sizes as $size ) {

                        if ( $size <= $width ) {

                            $resized_image = wpthumb( $original, 'width=' . $size . '&crop=0' );

                            $newimage .= ' data-media' . $breakpoint . '="' . $resized_image . '"';

                        }

                        $i++;
                        $breakpoint = $size;

                    }

                    // Fallback incase original image is smaller then smallest breakpoint

                    
                    if ( $width < $sizes[0] ) {

                    	$newimage .= ' data-media="' . $original . '"';

                    }
                    

                    $newimage .= '><noscript><img src="' . $original . '" alt="' . $alt . '" title="' . $title . '" width="' . $width . '" height="' . $height . '"></noscript></figure>';

                    $content = str_replace( $images[0][$index], $newimage, $content );

                }
				
			}
		
		}
		
	}
	
	return $content;
	
}

add_filter( 'the_content', 'hammy_replace_images', 999 );

?>
