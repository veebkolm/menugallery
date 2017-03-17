<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_shortcode( 'sg', 'simple_gallery_shortcode' );

function simple_gallery_shortcode( $post_id ) {
	ob_start();

	wp_enqueue_script( 'jquery.min.js', SG_URL . 'js/jquery.min.js' );
	// wp_enqueue_script( 'lazysizes.min.js', SG_URL . 'js/lazysizes.min.js' );
	wp_enqueue_script( 'isotope.min.js', SG_URL . 'js/isotope.min.js' );
	wp_enqueue_script( 'lightbox.js', SG_URL . 'js/lightbox.js' );
	wp_enqueue_style( 'lightbox', SG_URL . 'css/lightbox.css' );
	wp_enqueue_style( 'sg-output', SG_URL . 'css/sg-output.css' );
	wp_enqueue_script( 'script.js', SG_URL . 'js/script.js' );

	$gallery_settings = unserialize( base64_decode( get_post_meta( 
		$post_id[ 'id' ], 'simple_gallery' . $post_id[ 'id' ], true )
	) );
	// $extra_fields = $gallery_settings['extra-field'];
	$gallery_id = $post_id[ 'id' ];
	$default_category = isset($gallery_settings['default-category']) ? $gallery_settings['default-category'] : 'All';
	$lightbox = isset($gallery_settings['lightbox']) ? $gallery_settings['lightbox'] : false;

	require_once( 'simple-gallery-output.php' );
	return ob_get_clean();
}