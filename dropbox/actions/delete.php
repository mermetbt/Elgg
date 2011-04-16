<?php

/* Need to be logged in to do this */
gatekeeper();

/* Get the parameters : files to delete and path. */
$path = get_input('path');
$files = get_input('selected_files');

/* Transform the files data in an array */
if (!is_array($files)) {
	$files = array($files);
}

/* Check if there are files selected, and forward directly if not. */
foreach($files AS $file) {
	if($file) {
		$new_files[] = $file;
	}
}
if(!isset($new_files)) {
	register_error(elgg_echo("dropbox:error:nofilesselected"));
	forward($_SERVER['HTTP_REFERER']);
}
$files = $new_files;

/* Delete procedure */
try {
	/* Connection to dropbox. */
	$ret = dropbox_connect();
	if ($ret) {
		forward('pg/dropbox/error/?errcode=' . $ret);
	}

	/* Delete */
	foreach ($files AS $file) {
		$CONFIG->dropbox->delete($file);
	}

	system_message(elgg_echo('dropbox:deleted'));
	forward('pg/dropbox/root/?path=' . $path);
} catch (Dropbox_Exception_NotFound $e) {
	register_error(elgg_echo("dropbox:notfound"));
	forward($_SERVER['HTTP_REFERER']);
}

