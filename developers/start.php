<?php
/**
 * Elgg developer tools
 */

// we want to run this as soon as possible - other plugins should not need to do this
developers_process_settings();

elgg_register_event_handler('init', 'system', 'developers_init');

function developers_init() {
	elgg_register_event_handler('pagesetup', 'system', 'developers_setup_menu');

	elgg_extend_view('css/admin', 'developers/css');

	elgg_register_page_handler('theme_preview', 'developers_theme_preview_controller');

	$action_base = elgg_get_plugins_path() . 'developers/actions/developers';
	elgg_register_action('developers/settings', "$action_base/settings.php", 'admin');
}

function developers_process_settings() {
	if (elgg_get_plugin_setting('display_errors', 'developers') == 1) {
		ini_set('display_errors', 1);
	} else {
		ini_set('display_errors', 0);
	}
}

function developers_setup_menu() {
	if (elgg_in_context('admin')) {
		elgg_register_admin_menu_item('develop', 'settings', 'developers');
		elgg_register_admin_menu_item('develop', 'preview', 'developers');
		elgg_register_admin_menu_item('develop', 'languages_check', 'developers');
	}
}

/**
 * Serve the theme preview pages
 *
 * @param array $page
 */
function developers_theme_preview_controller($page) {
	if (!isset($page[0])) {
		forward('theme_preview/general');
	}

	$pages = array(
		'general', 
		'navigation', 
		'forms', 
		'objects', 
		'grid', 
		'widgets', 
		'icons',
	);
	
	foreach ($pages as $page_name) {
		elgg_register_menu_item('page', array(
			'name' => $page_name,
			'text' => elgg_echo("theme_preview:$page_name"),
			'href' => "theme_preview/$page_name",
		));
	}

	$title = elgg_echo("theme_preview:{$page[0]}");
	$body =  elgg_view("theme_preview/{$page[0]}");

	echo elgg_view_page($title, $body, 'theme_preview');
}
