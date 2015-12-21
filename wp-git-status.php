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

add_action( 'admin_init', 'wpgs_settings_init' );
add_action( 'admin_bar_menu', 'wpgs_git_info', 900 );

/**
 * Get git info
 */
function wpgs_get_git_info() {
	$active_theme      = wp_get_theme();
	$active_theme_name = $active_theme->get( 'Name' );
	$dir_themes        = exec( 'cd ' . get_stylesheet_directory() . ' && pwd 2>&1' );

	return $git_info;
}

/**
 * Add git info to admin bar
 */
function wpgs_git_info($wp_admin_bar) {
	$git_info = wpgs_get_git_info();
	$args = array(
		'id'        => 'git_status',
		'title'     => $git_info,
		'meta'      => array( 'class' => 'first-toolbar-group' ),
	);
	$wp_admin_bar->add_node( $args );
}
