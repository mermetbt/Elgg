<?php

/**
 * Elgg Dropbox page
 *
 * @package ElggDropbox
 */

/* You need to be logged in! */
gatekeeper();

/* */
elgg_push_breadcrumb(elgg_echo('dropbox:info'));

/* Set the page title */
$title = elgg_echo('dropbox:info');

$content = elgg_view('dropbox/status');

$body = elgg_view_layout('one_sidebar', array(
	'content' => $content,
	'title' => elgg_echo('dropbox:info'),
	'filter' => '',
));

/* Draw page */
echo elgg_view_page($title, $body);
