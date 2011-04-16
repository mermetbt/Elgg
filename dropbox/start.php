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
include(dirname(__FILE__) . '/lib/Dropbox/autoload.php');

/* Define all the status of the current Dropbox connection. */
define('DROPBOX_AUTH_OK', 0);
define('DROPBOX_CONSUMER_MISSING', 1);
define('DROPBOX_CONSUMER_FAILED', 2);
define('DROPBOX_USERPASS_MISSING', 3);
define('DROPBOX_USERPASS_FAILED', 4);
define('DROPBOX_FORBIDDEN', 5);

/**
 * Plugin's initialisation.
 *
 * @global <type> $CONFIG 
 */
function dropbox_init() {
	global $CONFIG;

	// Show the Dropbox menu on the topbar
	elgg_extend_view('elgg_topbar/extend', 'dropbox/topbar');

	// Submenu for the Dropbox content
	if (get_context() == "dropbox") {
		add_submenu_item(elgg_echo('dropbox:info'), $CONFIG->wwwroot . "pg/dropbox/");
		add_submenu_item(elgg_echo('dropbox:root'), $CONFIG->wwwroot . "pg/dropbox/root/");
		//add_submenu_item(elgg_echo('dropbox:put'), $CONFIG->wwwroot . "pg/dropbox/put/");
	}

	// Allow Dropbox plugin to have url formatted
	register_page_handler('dropbox', 'dropbox_page_handler');

	// Extend hover-over and profile menu
	elgg_extend_view('profile/menu/links', 'dropbox/menu');

	/* Add CSS rules. */
	elgg_extend_view('css', 'dropbox/css');
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
	$consumer_key = get_plugin_setting('consumer_key', 'dropbox');
	$consumer_secret = get_plugin_setting('consumer_secret', 'dropbox');
	if ($consumer_key && $consumer_secret) {

		/* check user settings */
		$user_id = get_loggedin_userid();
		$token = get_plugin_usersetting('token', $user_id, 'dropbox');
		$token_secret = get_plugin_usersetting('token_secret', $user_id, 'dropbox');

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

	/* Set the default username. */
	if (!isset($page[1])) {
		$page[1] = get_loggedin_user()->username;
	}

	/* Select the view to print. */
	switch ($page[0]) {
		/* Main page. */
		case 'index':
			set_input('username', $page[1]);
			include(dirname(__FILE__) . '/index.php');
			break;
		/* Listing of the files in the directory. */
		case 'root':
			set_input('username', $page[1]);
			include(dirname(__FILE__) . '/root.php');
			break;
		/* Upload a file. */
		case 'upload':
			include(dirname(__FILE__) . '/upload.php');
			break;
		/* Make a directory */
		case 'mkdir':
			include(dirname(__FILE__) . '/mkdir.php');
			break;
		/* Share a folder */
		case 'share':
			include(dirname(__FILE__) . '/share.php');
			break;
		/* Error page. */
		case 'error':
			set_input('username', $page[1]);
			if($ret)
			  set_input('errcode', $ret);
			include(dirname(__FILE__) . '/error.php');
			break;
		default:
			return false;
	}

	return true;
}

register_elgg_event_handler('init', 'system', 'dropbox_init');

global $CONFIG;

register_action('dropbox/share', false, $CONFIG->pluginspath . 'dropbox/actions/share.php');
register_action('dropbox/upload', false, $CONFIG->pluginspath . 'dropbox/actions/upload.php');
register_action('dropbox/mkdir', false, $CONFIG->pluginspath . 'dropbox/actions/mkdir.php');
register_action('dropbox/delete', false, $CONFIG->pluginspath . 'dropbox/actions/delete.php');
register_action('dropbox/getfile', false, $CONFIG->pluginspath . 'dropbox/actions/getfile.php');