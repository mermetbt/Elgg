<?php

/* You need to be logged in! */
gatekeeper();

/* Get parameters */
$path = get_input('path', '');

/* Set the page title */
$title = elgg_echo('dropbox:newfolder');

/* */
elgg_push_breadcrumb(elgg_echo('dropbox:root'), 'dropbox/root');
elgg_push_breadcrumb(elgg_echo('dropbox:newfolder'));

/* Set the page title */
$title = elgg_echo('dropbox:info');

$content = elgg_view('dropbox/mkdir', array('path' => $path));

$body = elgg_view_layout('one_sidebar', array(
	'content' => $content,
	'title' => elgg_echo('dropbox:newfolder'),
	'filter' => '',
));

/* Draw page */
echo elgg_view_page($title, $body);
