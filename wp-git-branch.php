<?php
/**
 * Plugin Name:    WP Git Branch
 * Plugin URI:     http://github.com/josephfusco/wp-git-branch/
 * Description:    Show active theme git branch and commit hash in the toolbar.
 * Version:        1.1.1
 * Author:         Joseph Fusco
 * Author URI:     http://josephfus.co/
 * License:        GPLv2 or later
 * License URI:    http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:    wp-git-branch
 * Domain Path:    /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_bar_menu', 'wpgb_git_info', 900 );
add_action( 'wp_enqueue_scripts', 'wpgb_enqueue' );
add_action( 'admin_enqueue_scripts', 'wpgb_enqueue' );
add_action( 'plugins_loaded', 'wpgb_plugin_textdomain' );

/**
 * Enqueue stylesheet
 *
 * @action wp_enqueue_scripts
 * @action admin_enqueue_scripts
 */
function wpgb_enqueue() {
	if ( ! is_super_admin() ) {
		return;
	}

	wp_enqueue_style( 'wpgb-style', plugins_url( '/css/style.css' , __FILE__ ) );
}

/**
 * Execute git commands
 */
function wpgb_git_rev( $path ) {
	return shell_exec( 'cd ' . $path . ' && git rev-parse --short HEAD && git rev-parse --abbrev-ref HEAD 2>&1' );
}

/**
 * Get git info
 */
function wpgb_get_git_info() {
	$directory       = exec( 'cd ' . get_stylesheet_directory() . ' && pwd 2>&1' );
	$name            = wp_get_theme();
	$git             = $directory . '/.git';
	$git_index       = $git . '/index';

	// Check if commits are present
	if ( file_exists( $git_index ) ) {
		return $name . ': <strong>' . wpgb_git_rev( $directory ) . '</strong>';
	} elseif ( file_exists( $git ) ) {
		return $name . ': ' . __( 'no commit history', 'wp-git-branch' );
	} else {
		return $name . ': ' . __( 'no git found', 'wp-git-branch' );
	}
}

/**
 * Add git info to toolbar
 *
 * @action admin_bar_menu
 */
function wpgb_git_info( $wp_admin_bar ) {
	if ( ! is_super_admin() ) {
		return;
	}

	$git_info = wpgb_get_git_info();
	$args = array(
		'id'    => 'git-branch',
		'title' => '<span class="ab-icon"></span><span class="ab-label">' . $git_info . '</span>',
		'meta'  => array(
			'class' => 'git-branch',
		),
	);

	$wp_admin_bar->add_node( $args );
}

/**
 * Load language files
 *
 * @action plugins_loaded
 */
function wpgb_plugin_textdomain() {
	load_plugin_textdomain( 'wp-git-branch', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

