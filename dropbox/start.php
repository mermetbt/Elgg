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

		// check user settings
		$user_id = get_loggedin_userid();
		$access_key = get_plugin_usersetting('access_key', $user_id, 'dropbox');
		$access_secret = get_plugin_usersetting('access_secret', $user_id, 'dropbox');
		if ($access_key && $access_secret) {
			/* Start the connector. */
			try {
				$oauth = new Dropbox_OAuth_ELGG($consumer_key, $consumer_secret);
				$dropbox = new Dropbox_API($oauth);
				
				/* Identification. */
				try {
					$tokens = $dropbox->getToken($access_key, $access_secret);
				} catch (Dropbox_Exception $e) {
					return DROPBOX_USERPASS_FAILED;
				}
				$oauth->setToken($tokens);

				/*
				 * Get account information. This initiate a request to Dropbox,
				 * and allow us to know if the consumer is good.
				 */
				$dropbox->getAccountInfo();
			} catch (Dropbox_Exception $e) {
				return DROPBOX_CONSUMER_FAILED;
			}

			$CONFIG->dropbox = $dropbox;
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
			include(dirname(__FILE__) . "/index.php");
			break;
		/* Listing of the files in the directory. */
		case 'root':
			set_input('username', $page[1]);
			include(dirname(__FILE__) . "/root.php");
			break;
//		case 'put':
//			set_input('username', $page[1]);
//			include(dirname(__FILE__) . "/put.php");
//			break;
		/* Error page. */
		case 'error':
			set_input('username', $page[1]);
			set_input('errcode', $ret);
			include(dirname(__FILE__) . "/error.php");
			break;
		default:
			return false;
	}

	return true;
}

/**
 * This method sort an array by a specific key and maintains index association.
 *
 * I found this method at this address http://php.net/manual/fr/function.sort.php
 * and is written by phpdotnet at m4tt dot co dot uk.
 * I improved the method to support empty keys.
 *
 * @param array $array Array to sort
 * @param string $on Key to sort
 * @param const $order Order (SORT_ASC/SORT_DESC)
 * @return array Sorted array
 */
function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
					$v2 = (empty($v2)) ? 2 : 1;
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

register_elgg_event_handler('init', 'system', 'dropbox_init');
