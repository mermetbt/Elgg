<?php

/* You need to be logged in! */
gatekeeper();

/* Get parameters */
$path = get_input('path', '');

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

/* */
elgg_push_breadcrumb(elgg_echo('dropbox:root'));

/* Set the page title */
$title = elgg_echo('dropbox:root');

$content = elgg_view('dropbox/root', array('path' => $path, 'files' => $files));

$body = elgg_view_layout('one_sidebar', array(
	'content' => $content,
	'title' => elgg_echo('dropbox:root'),
	'filter' => '',
));

/* Draw page */
echo elgg_view_page($title, $body);
