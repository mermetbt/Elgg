<?php

/* You need to be logged in! */
gatekeeper();

/* Get parameters */
$path = get_input('path', '');

/* */
elgg_push_breadcrumb(elgg_echo('dropbox:root'), 'dropbox/root');
elgg_push_breadcrumb(elgg_echo('upload'));

/* Set the page title */
$title = elgg_echo('upload');

$content = elgg_view('dropbox/upload', array('path' => $path));

$body = elgg_view_layout('one_sidebar', array(
	'content' => $content,
	'title' => elgg_echo('upload'),
	'filter' => '',
));

/* Draw page */
echo elgg_view_page($title, $body);
