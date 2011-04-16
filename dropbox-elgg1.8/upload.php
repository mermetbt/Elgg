<?php

/* Load Elgg engine */
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

/* You need to be logged in! */
gatekeeper();

$page_owner = get_loggedin_user();
set_page_owner($page_owner->getGUID());

/* Set the page title */
$area2 = elgg_view_title(elgg_echo("upload"));

/* Get parameters */
$path = get_input('path');
if (!isset($path)) {
	$path = '';
}

$area2 .= elgg_view('dropbox/upload', array('path' => $path));

$body = elgg_view_layout('two_column_left_sidebar', array('content' => $area2));

/* Draw page */
echo elgg_view_page(sprintf(elgg_echo('dropbox:user'), $page_owner->name), $body);
