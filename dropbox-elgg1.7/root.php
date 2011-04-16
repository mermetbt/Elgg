<?php

/* Load Elgg engine */
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

/* You need to be logged in! */
gatekeeper();

$page_owner = get_loggedin_user();
set_page_owner($page_owner->getGUID());

/* Set the page title */
$area2 = elgg_view_title(elgg_echo("dropbox:root"));

/* Get parameters */
$path = get_input('path');
if (!isset($path)) {
	$path = '';
}

/* Get listing of the root directory. */
global $CONFIG;
$dropbox = $CONFIG->dropbox;
try {
	$files = $dropbox->getLinks($path);
} catch(Dropbox_Exception_Forbidden $e) {
	forward('pg/dropbox/error/?errcode=5');
} catch(Dropbox_Exception $e) {
	forward('pg/dropbox/error/');
}

$area2 .= elgg_view('dropbox/root', array('path' => $path, 'files' => $files));

$body = elgg_view_layout("two_column_left_sidebar", '', $area2);

/* Draw page */
page_draw(sprintf(elgg_echo('dropbox:user'), $page_owner->name), $body);


