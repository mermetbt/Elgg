<?php

/* You need to be logged in! */
gatekeeper();

/* Get parameters */
$path = get_input('path');
if (!isset($path)) {
	$path = '';
}

/* */
elgg_push_breadcrumb(elgg_echo('dropbox:root'), 'dropbox/root');
elgg_push_breadcrumb(elgg_echo('dropbox:sharefolder'));

/* Set the page title */
$title = elgg_echo('dropbox:sharefolder');


$content = elgg_view('dropbox/share', array('path' => $path));

$body = elgg_view_layout('one_sidebar', array(
	'content' => $content,
	'title' => elgg_echo('dropbox:sharefolder'),
	'filter' => '',
));

/* Draw page */
echo elgg_view_page($title, $body);
