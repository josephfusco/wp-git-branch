<?php
/*
Plugin Name:		WP Git Status
Plugin URI:		http://github.com/josephfusco/git-status/
Description:		Shows git branch and commit hash in the admin bar.
Version:		0.0.1
Author:			Joseph Fusco
Author URI:		http://josephfus.co/
License:		GPLv2 or later
*/

if ( !defined( 'ABSPATH' ) )
	exit;

add_action( 'admin_menu', 'wpgs_add_admin_menu' );
add_action( 'admin_init', 'wpgs_settings_init' );

/**
 *
 * Get themes that have git present
 *
 */
function wpgs_get_themes() {
	$themes = array();
	foreach(wp_get_themes() as $index=>$theme) {
		$filename = WP_CONTENT_DIR . '/themes/'.$index.'/.git';
		if (file_exists($filename)) {
			$themes[$index] = array(
				'directory' => $index,
				'name' => $theme->get('Name')
			);
		}
	}
	return $themes;
}

function wpgs_add_admin_menu() {
	add_options_page( 'Git Status', 'Git Status', 'manage_options', 'wpgs', 'wpgs_options_page' );
}

function wpgs_settings_init() {
	register_setting( 'pluginPage', 'wpgs_settings' );
	add_settings_section(
		'wpgs_pluginPage_section',
		__( 'Themes', 'wordpress' ),
		'wpgs_settings_section_callback',
		'pluginPage'
	);
	add_settings_field(
		'wpgs_select_field_0',
		__( 'Available with git present', 'wordpress' ),
		'wpgs_select_field_0_render',
		'pluginPage',
		'wpgs_pluginPage_section'
	);
}

/**
 *
 * Render select element with available themes containing git.
 *
 */
function wpgs_select_field_0_render() {
	$options = get_option( 'wpgs_settings' );
	$themes = wpgs_get_themes();

	echo "<select name='wpgs_settings[wpgs_select_field_0]'>";
	foreach($themes as $theme) {
		echo "<option value='" . $theme['directory'] . "'" . selected( $options['wpgs_select_field_0'], $theme['directory'] ) . ">" . $theme['name'] . "</option>";
	}
	echo "</select>";
}


function wpgs_settings_section_callback() {
	echo __( 'Choose WordPress plugins and/or themes that you would like to display the git status of in the admin bar.', 'wordpress' );
}


function wpgs_options_page() {
	?>
	<form class="wrap" action='options.php' method='post'>

		<h2>WP Git Status</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		$options = get_option( 'wpgs_settings' );
		echo '<pre>'.print_r($options).'</pre>';
		?>

	</form>
	<?php
}
