<?php
/*
Plugin Name:    WP Git Status
Plugin URI:     http://github.com/josephfusco/git-status/
Description:    Show active theme git branch and commit hash in the admin bar.
Version:        1.0.0
Author:         Joseph Fusco
Author URI:     http://josephfus.co/
License:        GPLv2 or later
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
	$theme_directory   = exec( 'cd ' . get_stylesheet_directory() . ' && pwd 2>&1' );
	$git = $theme_directory.'/.git';

	// Check if git is present
	if(file_exists($git)){
		return wp_get_theme() . ': ' . str_replace("\n", ' ', wpgs_git_rev( $theme_directory ));
	} else {
		return wp_get_theme() . ': git not present';
	}
}

/**
 * Add git info to admin bar
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
