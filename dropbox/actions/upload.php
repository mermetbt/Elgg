<?php

/* Need to be logged in to do this. */
gatekeeper();

/* Get the path. */
$path = get_input('path');

/* Get the file. */
$file = $_FILES['file']['name'];

/* Redirect the user if Cancel is clicked. */
$submit = get_input('submit');
if($submit == elgg_echo('cancel')) {
	forward('pg/dropbox/root/?path=' . $path);
}

/* Upload file procedure */
try {
	/* Connection to dropbox. */
	$ret = dropbox_connect();
	if ($ret) {
		forward('pg/dropbox/error/?errcode=' . $ret);
	}

	$CONFIG->dropbox->putFile($path . '/' . $file, $_FILES['file']['tmp_name']);

	system_message(sprintf(elgg_echo('dropbox:uploaded'), $file));
	forward('pg/dropbox/root/?path=' . $path);
} catch (Dropbox_Exception_NotFound $e) {
	register_error(elgg_echo("dropbox:notfound"));
	forward($_SERVER['HTTP_REFERER']);
}

