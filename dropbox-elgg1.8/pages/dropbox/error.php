<?php

/* You need to be logged in! */
gatekeeper();

$err = get_input('errcode');

elgg_push_breadcrumb(elgg_echo('dropbox:error'));

/* Set the page title. */
$title = elgg_echo('dropbox:error');

$content = elgg_view('dropbox/error', array('errcode' => $err));

$body = elgg_view_layout('one_sidebar', array(
	'content' => $content,
	'title' => elgg_echo('dropbox:error'),
	'filter' => '',
));

/* Draw page */
echo elgg_view_page($title, $body);

