<?php

/**
 * Elgg dropbox widget
 * 
 * This plugin allows users to connect their Elgg account to dropbox so
 * that users can share large files.
 * 
 * @package ElggDropbox
 */

/* Include the Dropbox modified API. */
include(dirname(__FILE__) . '/vendors/dropboxoauth/Dropbox/autoload.php');

/* Define all the status of the current Dropbox connection. */
define('DROPBOX_AUTH_OK', 0);
define('DROPBOX_CONSUMER_MISSING', 1);
define('DROPBOX_CONSUMER_FAILED', 2);
define('DROPBOX_USERPASS_MISSING', 3);
define('DROPBOX_USERPASS_FAILED', 4);
define('DROPBOX_FORBIDDEN', 5);
define('DROPBOX_NOTFOUND', 6);

/**
 * Plugin's initialisation.
 *
 * @global <type> $CONFIG
 * @return nothing
 */
function dropbox_init() {
	global $CONFIG;
	
	/* Show the Dropbox menu on the topbar. */
	elgg_register_menu_item('topbar', array(
		'name' => 'dropbox',
		'href' => 'dropbox/',
		'text' => '<img class="sprite s_dropbox_blue" alt="">',
		'priority' => 500,
	));

	/* Submenu for the Dropbox content. */
	if (elgg_get_context() == "dropbox") {
		elgg_register_menu_item('page', array(
			'name' => 'dropbox:info',
			'text' => elgg_echo('dropbox:info'),
			'href' => 'dropbox/',
			'context' => 'dropbox',
		));
		elgg_register_menu_item('page', array(
			'name' => 'dropbox:root',
			'text' => elgg_echo('dropbox:root'),
			'href' => 'dropbox/root/',
			'context' => 'dropbox',
		));
	}

	/* Allow Dropbox plugin to have url formatted */
	elgg_register_page_handler('dropbox', 'dropbox_page_handler');

	/* Extend hover-over and profile menu */
	elgg_extend_view('profile/menu/links', 'dropbox/menu');

	/* Add CSS rules. */
	elgg_extend_view('css/elgg', 'dropbox/css');

	/* Register actions. */
	$action_path = elgg_get_plugins_path() . 'dropbox/actions/dropbox';
	elgg_register_action('dropbox/share', $action_path . '/share.php');
	elgg_register_action('dropbox/upload', $action_path . '/upload.php');
	elgg_register_action('dropbox/mkdir', $action_path . '/mkdir.php');
	elgg_register_action('dropbox/delete', $action_path . '/delete.php');
	elgg_register_action('dropbox/getfile', $action_path . '/getfile.php');
	
	/* User settings actions. */
	elgg_register_action('dropbox/unlink', $action_path . '/unlink.php');
	
}

/**
 * This function is used to connect Elgg to the Dropbox website.
 *
 * @global  $CONFIG
 * @return Connection status.
 */
function dropbox_connect() {
	global $CONFIG;

	/* Initialize Dropbox connection. */
	$consumer_key = elgg_get_plugin_setting('consumer_key', 'dropbox');
	$consumer_secret = elgg_get_plugin_setting('consumer_secret', 'dropbox');
	if ($consumer_key && $consumer_secret) {

		/* check user settings */
		$user_id = elgg_get_logged_in_user_guid();
		$token = elgg_get_plugin_user_setting('token', $user_id, 'dropbox');
		$token_secret = elgg_get_plugin_user_setting('token_secret', $user_id, 'dropbox');

		if ($token && $token_secret) {
			try {
				$oauth = new Dropbox_OAuth_ELGG($consumer_key, $consumer_secret);
				$dropbox = new Dropbox_API($oauth);
				$CONFIG->dropbox = $dropbox;
				try {
					$oauth->setToken($token, $token_secret);
					/*
					 * Get account information. This initiate a request to Dropbox,
					 * and allow us to know if the consumer is good.
					 */
					$dropbox->getAccountInfo();
				} catch (Dropbox_Exception $e) {
					return DROPBOX_USERPASS_FAILED;
				}
			} catch (Dropbox_Exception $e) {
				return DROPBOX_CONSUMER_FAILED;
			}
		} else {
			return DROPBOX_USERPASS_MISSING;
		}
	} else {
		return DROPBOX_CONSUMER_MISSING;
	}
	return DROPBOX_AUTH_OK;
}

/**
 * This function is the Dropbox page handler.
 *
 * It allows the Dropbox plugin to have nice URL format.
 * All the redirection are defined in this function.
 *
 * @param array $page From the page_handler function
 * @return true|false Depending on success
 */
function dropbox_page_handler($page) {

	/* Starting connection. If we obtain an error, we redirect to the error page. */
	switch ($ret = dropbox_connect()) {
		case DROPBOX_AUTH_OK:
			break;
		case DROPBOX_CONSUMER_FAILED:
		case DROPBOX_CONSUMER_MISSING:
		case DROPBOX_USERPASS_FAILED:
		case DROPBOX_USERPASS_MISSING:
		default:
			$page[0] = "error";
	}

	/* Set the default page. */
	if (!isset($page[0])) {
		$page[0] = 'index';
	}

	elgg_push_breadcrumb(elgg_echo('dropbox'), 'dropbox');

	$pages_path = elgg_get_plugins_path() . 'dropbox/pages/dropbox';

	/* Select the view to print. */
	switch ($page[0]) {
		/* Main page. */
		case 'index':
			include($pages_path . '/index.php');
			break;
		/* Listing of the files in the directory. */
		case 'root':
			include($pages_path . '/root.php');
			break;
		/* Upload a file. */
		case 'upload':
			include($pages_path . '/upload.php');
			break;
		/* Make a directory */
		case 'mkdir':
			include($pages_path . '/mkdir.php');
			break;
		/* Share a folder */
		case 'share':
			include($pages_path . '/share.php');
			break;
		/* Error page. */
		case 'error':
			if ($ret) {
				set_input('errcode', $ret);
			}
			include($pages_path . '/error.php');
			break;
		default:
			return false;
	}

	return true;
}

elgg_register_event_handler('init', 'system', 'dropbox_init');

