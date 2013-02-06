<?php
/*
Plugin Name: Hammy
Plugin URI: http://wordpress.org/extend/plugins/hammy/
Description: Creates responsive images for your content area with breakpoints that you set.
Author: Noel Tock
Version: 1.0.1
Author URI: http://www.noeltock.com
*/

/**
 * Defines
 */
define ( 'HAMMY_VERSION', '1.0' );
define ( 'HAMMY_PATH',  WP_PLUGIN_URL . '/' . end( explode( DIRECTORY_SEPARATOR, dirname( __FILE__ ) ) ) );

/**
 * Register Default Settings
 */
if (get_option('hammy_options')== '') {
    register_activation_hook(__FILE__, 'hammy_defaults');
}

function hammy_defaults() {
    $arr = array('hammy_breakpoints' => '320,480,768', 'hammy_ignores' => 'nextgen, thumbnail', 'hammy_parent' => '.entry-content', 'hammy_lazy' => '');
    update_option('hammy_options', $arr);
}

/**
 * Check for WPThumb, include.
 * 
 */
if ( !function_exists('wpthumb') ) {
	include_once('WPThumb/wpthumb.php');
}

/**
 * Plugin Options
 */
include_once('includes/options.php');

add_action('admin_init', 'hammy_options_init' );
add_action('admin_menu', 'hammy_add_page');


/**
 * Enqueue & Localize JavaScript & CSS
 */
function load_hammy_js() {

	$options = get_option('hammy_options');
	
	
	
	if ($options['hammy_lazy']) {
		wp_enqueue_script( 'jquery-picture', HAMMY_PATH . '/js/jquery-picture-lazy.js', array('jquery'),null,true );
		wp_enqueue_script('lazyload', HAMMY_PATH . '/js/jquery.lazyload.min.js', array('jquery'),null, true );
		wp_enqueue_script( 'hammy', HAMMY_PATH . '/js/hammy-lazy.js', array('jquery'),null,true  );
	} else {
		wp_enqueue_script( 'jquery-picture', HAMMY_PATH . '/js/jquery-picture.js', array('jquery'),null,true );
		wp_enqueue_script( 'hammy', HAMMY_PATH . '/js/hammy.js', array('jquery'),null,true  );
	}
	
    wp_localize_script( 'hammy', 'imageParent', $options['hammy_parent'] );
		
}

add_action('wp_print_scripts', 'load_hammy_js');

function load_hammy_css() {

    wp_enqueue_style('hammy-css', HAMMY_PATH . '/css/hammy-admin.css');

}

add_action('admin_print_styles', 'load_hammy_css');

/**
 * Hammy Time
 *
 * @param $content
 * @return DOM element with fallback (<picture> -> <img>)
 */
function hammy_replace_images( $content ) {

	global $post;

    $options = get_option('hammy_options');
	
	preg_match_all('/<img (.*?)\/>/', $content, $images);
	
	if( !is_null( $images ) ) {
	
		foreach( $images[0] as $index => $value) {
		
			$doc = new DOMDocument();
			$doc->loadHTML($value);
			$items = $doc->getElementsByTagName('img');
				
			foreach($items as $item) {

				// Get Attributes
			
				$original = $item->getAttribute('src');
				$width = $item->getAttribute('width');
				$class = $item->getAttribute('class');
				$alt = $item->getAttribute('alt');

                // Check if it's part of an ignored class

                $ignoreClasses = explode(",", $options['hammy_ignores']);

                $ignorelist = '/' . implode("|", $ignoreClasses) . '/';

                if ( !preg_match( $ignorelist, $class) ) {
				
                    // Get Sizes

                    $sizes = explode(",", $options['hammy_breakpoints']);

                    // Render Sizes

                    $i = 0;
                    $breakpoint = null;

                    // Output & Replace Strings

                    $newimage = '<picture class="hammy-responsive ' . $class . '" alt="' . $alt . '">';

                    foreach ($sizes as $size) {

                        if ( $i == 0 ) {

                            $media = null;

                        } else {

                            $media = ' media="(min-width:' . $breakpoint . 'px)"';

                        }

                        if ( $size <= $width ) {


                            $resized_image = wpthumb( $original, 'width=' . $size . '&crop=0' );

                            $newimage .= '<source src="' . $resized_image . '"' . $media . '>';

                        }

                        $i++;
                        $breakpoint = $size;

                    }

                    $newimage .= '<noscript><img src="' . $original . '" alt="' . $alt . '"></noscript></picture>';

                    $content = str_replace($images[0][$index], $newimage, $content);

                }
				
			}
		
		}
		
	}
	
	return $content;
	
}

add_filter('the_content', 'hammy_replace_images', 999);


/**
 * Hammy, please give me some adaptive images even if I'm not using the_content();
 * Modified by Jacques Letesson (@jacquesletesson)
 *
 * @params
 *	- $id (int) - ID of the image
 *	- $class (string) - Class(es) of the image
 *	- $size (string) - The thumbnail size to use
 *	- $caption (boolean) - Display the caption of the image, or not
 *
 * @return DOM element with fallback (<picture> -> <img>)
 */
function hammy_please_replace_images( $id, $class = "", $size = "large", $caption = false ) {

	$options = get_option('hammy_options');
	// Get Sizes
	$sizes = explode(",", $options['hammy_breakpoints']);
	// Render Sizes
	$i = 0;
	$breakpoint = null;
	
	// Get the image and its metadata
	$original = wp_get_attachment_image_src( $id, $size ); 
	$metadata = wp_get_attachment_metadata($id);
	$data = get_post($id);
	// Get the caption and the alt text
	$caption = $data->post_excerpt;
	$alt = get_post_meta($id, '_wp_attachment_image_alt', true);
	
	list($width, $height, $type, $attr)= getimagesize($original[0]);

	$newimage = '<picture class="hammy-responsive ' . $class . '" alt="' . $alt . '">';

		foreach ($sizes as $size) {

			if ( $i == 0 ) {

				$media = null;

			} else {

				$media = ' media="(min-width:' . $breakpoint . 'px)"';

			}

			if ( $size <= $width ) {

				$resized_image = wpthumb( $original[0], 'width=' . $size . '&crop=0' );

				$newimage .= '<source src="' . $resized_image . '"' . $media . '>';

			}

			$i++;
			
			$breakpoint = $size;
			
			}

		$newimage .= '<noscript><img src="' . $original[0]. '" alt="' . $alt . '"></noscript>';
		
			// If has a caption display it inside a figcaption
			if ($caption) {
			
				$newimage .= '<figcaption>' . $caption . '</figcaption></picture>';
			
			} else {
			
				$newimage .= '</picture>';
			
			}
		
		echo $newimage;

}

?>

