<?php

/* Need to be logged in to do this. */
gatekeeper();

/* Get the file. */
$file = get_input('file');

if (!$file) {
	register_error(elgg_echo("dropbox:notfound"));
	forward($_SERVER['HTTP_REFERER']);
}

/* Get file procedure. */
try {
	/* Connection to dropbox. */
	$ret = dropbox_connect();
	if ($ret) {
		forward('pg/dropbox/error/?errcode=' . $ret);
	}

	$content = $CONFIG->dropbox->getFile($file);

	header('Content-type: text/plain');
	header('Content-Disposition: attachment; filename="' . basename($file) . '"');

	echo $content;
	die();
} catch (Dropbox_Exception_NotFound $e) {
	register_error(elgg_echo("dropbox:notfound"));
	forward($_SERVER['HTTP_REFERER']);
}
