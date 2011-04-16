<?php

/* Need to be logged in to do this. */
gatekeeper();

/* Get the path. */
$path = get_input('path');

/* Get the name of the new folder. */
$folder = get_input('name');

/* Redirect the user if Cancel is clicked. */
$submit = get_input('submit');
if($submit == elgg_echo('cancel')) {
	forward('pg/dropbox/root/?path=' . $path);
}

/* Create folder procedure */
try {
	/* Connection to dropbox. */
	$ret = dropbox_connect();
	if ($ret) {
		forward('pg/dropbox/error/?errcode=' . $ret);
	}

	/* Create folder */
	$CONFIG->dropbox->createFolder($path . '/' . $folder);

	system_message(sprintf(elgg_echo('dropbox:folder:created'), $path . '/' . $folder));
	forward('pg/dropbox/root/?path=' . $path);
} catch (Dropbox_Exception_NotFound $e) {
	register_error(elgg_echo("dropbox:notfound"));
	forward($_SERVER['HTTP_REFERER']);
}
