<?php

/**
 * Elgg Dropbox page
 *
 * @package ElggDropbox
 */

/* Load Elgg engine */
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

/* You need to be logged in! */
gatekeeper();


$page_owner = get_loggedin_user();
set_page_owner($page_owner->getGUID());

/* Set the page title */
$area2 = elgg_view_title(elgg_echo("dropbox:info"));

$area2 .= elgg_view('dropbox/status');

$body = elgg_view_layout('two_column_left_sidebar', array('content' => $area2));

/* Draw page */
echo elgg_view_page(sprintf(elgg_echo('dropbox:user'), $page_owner->name), $body);
