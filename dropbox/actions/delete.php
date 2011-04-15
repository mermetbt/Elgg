<?php

/* Need to be logged in to do this */
gatekeeper();

$files = get_input('selected_files');
$path = get_input('path');

$success = 1;

if ($success) {
	if ($submit == elgg_echo('delete')) {
		system_message(elgg_echo("dropbox:deleted"));
	}
	
	forward("pg/dropbox/root/?path={$path}");

} else {
	register_error(elgg_echo("dropbox:notfound"));
	forward($_SERVER['HTTP_REFERER']);
}

