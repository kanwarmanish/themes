<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package AltoFocus
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function altofocus_body_classes( $classes ) {

	// Add class if no custom header or featured images
	$header_image = get_header_image();

	// Header text display class, determines size of logo
	$header_text_display = get_theme_mod( 'header_textcolor' );

	if ( $header_text_display == 'blank' ) {

		$classes[] = 'hide-site-title-description';
	}

	// Add class if footer image has been added
	$media_override = get_theme_mod( 'altofocus_media_override' );

	if ( $media_override == 'override' && is_singular() ) {

		$classes[] = 'media-override';
	}

	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {

		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {

		$classes[] = 'hfeed';
	}

	// Add a class of no-sidebar when there is no sidebar present
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {

		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'altofocus_body_classes' );

/**
 * Adds custom classes to the array of post classes.
 *
 * @param array $classes Classes for the article element.
 * @return array
 */
function altofocus_post_classes( $classes ) {

	global $post, $content_width;

	// Use 1/2 of the $content_width variable as the threshold for images that are too small
	$image_size_threshhold = $content_width / 2;

	if ( ! is_singular() ) {

		$classes[] = 'grid-item';

		// Get image meta
		$image_meta  = wp_get_attachment_metadata( get_post_thumbnail_id( $post->ID ) );

		// Set orientation and image constraint classes
		if ( isset( $image_meta['width'], $image_meta['height'] ) ) {

			// Use small classes when original image size is too small
			if ( $image_meta['width'] <= $image_size_threshhold || $image_meta['height'] <= $image_size_threshhold ) {

				$classes[] = 'grid-item-small';
			}

			// Use small class to make the grid-item images size conform to post-title size instead of image size
			if ( ( $image_meta['width'] > $image_meta['height'] ) &&
			   ( ( $image_meta['width'] / $image_meta['height'] ) <= 1 ) ) {

				$classes[] = 'grid-item-small';
			}

			// Landscape images
			if ( $image_meta['width'] >= $image_meta['height'] ) {

				$classes[] = 'grid-item-landscape';

			// Portrait images
			} else {

				$classes[] = 'grid-item-portrait';
			}
		}

		// Get featured content settings and options
		$featured_options    = get_option( 'featured-content' );
		$featured_tag_name   = $featured_options[ 'tag-name' ];

		if ( ! empty( $featured_tag_name ) && has_tag( $featured_tag_name, $post->ID ) ) {

			$classes[] = 'grid-item-featured';
		}
	}

	return $classes;
}
add_filter( 'post_class', 'altofocus_post_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function altofocus_pingback_header() {

	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'altofocus_pingback_header' );
