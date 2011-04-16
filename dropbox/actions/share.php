<?php

/* Need to be logged in to do this. */
gatekeeper();

/* Get the datas. */
$path = get_input('path');

$choice = get_input('choice');

$name = get_input('name');

$selectedusers = get_input('selectedusers');

/* Redirect the user if Cancel is clicked. */
$submit = get_input('submit');
if($submit == elgg_echo('cancel')) {
	forward('pg/dropbox/root/?path=' . $path);
}

