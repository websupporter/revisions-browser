<?php
/**
 * Plugin Name: Revision Browser
 * Plugin URI: https://github.com/Shelob9/revisions-browser
 * Description: Browse single post revisions via WP REST API.
 * Author: shelob9, mrahmadawais
 * Version: 0.1.1
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package RBR
 */

// Enqueue the script.
add_action( 'wp_enqueue_scripts', 'revisions_browser' );

/**
 * Main function for revision browser.
 *
 * @since  0.1.0
 */
function revisions_browser() {
	// Don't show to non editors.
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	// Let's just do single post view because POC.
	if ( ! is_single() ) {
		return;
	}

	add_action( 'admin_bar_menu', 'revisions_browser_toolbar', 999 );

	wp_enqueue_script( 'revision-browser', plugin_dir_url(__FILE__) . '/revision-browser.js', array( 'jquery', 'wp-api' ) );

	$selectors = wp_parse_args( get_theme_support( 'revision-browser-selectors' ), array(
		'content' => 'entry-content',
		'title'   => 'entry-title',
	) );

	global  $post;
	if ( ! is_object( $post ) ) {
		return;
	}

	// REVBROWSER object.
	wp_localize_script( 'revision-browser', 'REVBROWSER', [
		'post'    => absint( $post->ID ),
		'content' => $selectors[ 'content' ],
		'title'   => $selectors[ 'title' ],
		'none' => esc_html__( 'No Revisions')

	]);
};

/**
 * Add revision browser to toolbar
 *
 * @since 0.1.0
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function revisions_browser_toolbar(  $wp_admin_bar ) {
	$parent = 'revisions-browser';
	$meta = array( 'class' => 'revisions-browser' );
	$args = array(
		'id'    => $parent,
		'title' => __( 'Browse Revisions' ),
		'href'  => '#revisions',
		'meta'  => $meta
	);

	$wp_admin_bar->add_node( $args );

	$wp_admin_bar->add_node( array(
		'parent' => $parent,
		'id' => $parent . '-previous',
		'title' => __( 'Previous' ),
		'href' => '#previous-revision',
		'meta' => $meta,
	) );

	$wp_admin_bar->add_node( array(
		'parent' => $parent,
		'id' => $parent . '-next',
		'title' => __( 'Next' ),
		'href' => '#next-revision',
		'meta' => $meta,
	) );

}
