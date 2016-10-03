<?php
/**
 * Plugin Name:    WP Git Branch
 * Plugin URI:     http://github.com/josephfusco/wp-git-status/
 * Description:    Show active theme git branch and commit hash in the toolbar.
 * Version:        1.1.1
 * Author:         Joseph Fusco
 * Author URI:     http://josephfus.co/
 * License:        GPLv2 or later
 * License URI:    http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_bar_menu', 'wpgb_git_info', 900 );
add_action( 'wp_enqueue_scripts', 'wpgb_enqueue' );
add_action( 'admin_enqueue_scripts', 'wpgb_enqueue' );

/**
 *
 * Enqueue stylesheet
 *
 */
function wpgb_enqueue() {
	if(!is_super_admin())
		return false;
	wp_enqueue_style( 'wpgb-style', plugins_url( '/css/style.css' , __FILE__ ) );
}

/**
 *
 * Execute git commands
 *
 */
function wpgb_git_rev( $path ) {
	return shell_exec( 'cd ' . $path . ' && git rev-parse --short HEAD && git rev-parse --abbrev-ref HEAD 2>&1' );
}

/**
 *
 * Get git info
 *
 */
function wpgb_get_git_info() {
	$theme_directory = exec( 'cd ' . get_stylesheet_directory() . ' && pwd 2>&1' );
	$git             = $theme_directory . '/.git';
	$git_index       = $git . '/index';

	// Check if commits are present
	if(file_exists($git_index)){
		return wp_get_theme() . ': <strong>' . wpgb_git_rev( $theme_directory ) . '</strong>';
	} elseif(file_exists($git)) {
		return wp_get_theme() . ': no commit history';
	} else {
		return wp_get_theme() . ': no git found';
	}
}

/**
 *
 * Add git info to toolbar
 *
 */
function wpgb_git_info( $wp_admin_bar ) {
	if(!is_super_admin())
		return false;
	
	$git_info = wpgb_get_git_info();
	$args = array(
		'id'    => 'git-branch',
		'title' => '<span class="ab-icon"></span><span class="ab-label">' . $git_info . '</span>',
		'meta'  => array(
            'class' => 'git-branch'
        )
	);
	$wp_admin_bar->add_node( $args );
}
