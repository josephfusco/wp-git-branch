<?php
/**
 * Plugin Name:    WP Git Branch
 * Plugin URI:     http://github.com/josephfusco/wp-git-branch/
 * Description:    Show active theme git branch and commit hash in the toolbar.
 * Version:        1.1.2
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

class WP_Git_Branch {

	function __construct() {
		$this->load_menu();
		$this->load_styles();
		$this->load_textdomain();
	}

	function load_menu() {
		add_action( 'admin_bar_menu', array( $this, 'create_menu' ), 900 );
	}

	function load_styles() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	function load_textdomain() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	function enqueue_styles() {
		wp_enqueue_style( 'wpgb-style', plugins_url( '/css/style.css' , __FILE__ ) );
	}

	function create_menu( $wp_admin_bar ) {
		$git_info = $this->get_git_info();

		$args = array(
			'id'    => 'wpgb',
			'title' => '<span class="ab-icon"></span><span class="ab-label">' . $git_info . '</span>',
			'meta'  => array(
				'class' => 'git-branch',
			),
		);

		$wp_admin_bar->add_node( $args );
	}

	private function get_git_info() {
		$directory = get_stylesheet_directory();
		$name      = wp_get_theme();
		$git       = $directory . '/.git';
		$git_index = $git . '/index';
		$git_rev   = $this->git_rev( $directory );

		if ( file_exists( $git_index ) ) {
			// If commits are present
			return $name . ': <strong>' . esc_html( $git_rev ) . '</strong>';
		} elseif ( file_exists( $git ) ) {
			// If git exists with no commits
			return $name . ': ' . __( 'no commit history', 'wp-git-branch' );
		} else {
			// If git is not present
			return $name . ': ' . __( 'no git found', 'wp-git-branch' );
		}
	}

	private function git_rev( $path ) {
		return shell_exec( 'cd ' . $path . ' && git rev-parse --short HEAD && git rev-parse --abbrev-ref HEAD 2>&1' );
	}
}

$wp_git_branch = new WP_Git_Branch();
