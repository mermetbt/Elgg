<?php

/* Need to be logged in to do this */
gatekeeper();

/* Get the parameters : files to delete and path. */
$files = get_input('selected_files');
if (!is_array($files)) {
	$files = array($files);
}
$path = get_input('path');

/* Delete procedure */
try {
	/* Connection to dropbox. */
	$ret = dropbox_connect();
	if ($ret) {
		forward("pg/dropbox/error/", array('errcode' => $ret));
	}

	/* Delete */
	foreach ($files AS $file) {
		if ($file) {
			$CONFIG->dropbox->delete($file);
		}
	}

	system_message(elgg_echo('dropbox:deleted'));
	forward("pg/dropbox/root/?path={$path}");
} catch (Dropbox_Exception_NotFound $e) {
	register_error(elgg_echo("dropbox:notfound"));
	forward($_SERVER['HTTP_REFERER']);
}

