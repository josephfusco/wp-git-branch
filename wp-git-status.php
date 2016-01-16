<?php
/*
Plugin Name:    WP Git Status
Plugin URI:     http://github.com/josephfusco/wp-git-status/
Description:    Show active theme git branch and commit hash in the toolbar.
Version:        1.0.0
Author:         Joseph Fusco
Author URI:     http://josephfus.co/
License:        GPLv2 or later
License URI:    http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( !defined( 'ABSPATH' ) )
	exit;

add_action( 'admin_bar_menu', 'wpgs_git_info', 900 );

/**
 * Execute git commands
 */
function wpgs_git_rev( $path ) {
	return shell_exec( 'cd ' . $path . ' && git rev-parse --short HEAD && git rev-parse --abbrev-ref HEAD 2>&1' );
}

/**
 * Get git info
 */
function wpgs_get_git_info() {
	$theme_directory = exec( 'cd ' . get_stylesheet_directory() . ' && pwd 2>&1' );
	$git             = $theme_directory . '/.git';
	$git_index       = $git . '/index';

	// Check if commits are present
	if(file_exists($git_index)){
		return wp_get_theme() . ': <strong style="font-family:monospace">' . wpgs_git_rev( $theme_directory ) . '</strong>';
	} elseif(file_exists($git)) {
		return wp_get_theme() . ': no commit history';
	} else {
		return wp_get_theme() . ': no git found';
	}
}

/**
 * Add git info to toolbar
 */
function wpgs_git_info($wp_admin_bar) {
	$git_info = wpgs_get_git_info();
	$args = array(
		'id'    => 'git_status',
		'title' => $git_info,
		'meta'  => array( 'class' => 'first-toolbar-group' ),
	);
	$wp_admin_bar->add_node( $args );
}
