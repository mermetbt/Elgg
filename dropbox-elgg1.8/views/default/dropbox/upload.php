<?php

$path = $vars['path'];

$body = elgg_echo('dropbox:upload:info', array($path)) . '<br>';

/* Add path parameter to the form */
$body .= elgg_view('input/hidden', array(
			'name' => 'path',
			'value' => $path,
		));

$body .= elgg_view('input/file', array(
			'name' => 'file',
		));

$body .= elgg_view('input/submit', array(
			'name' => 'submit',
			'value' => elgg_echo('upload'),
			'class' => 'elgg-button elgg-button-submit',
		));

$body .= elgg_view('input/submit', array(
			'name' => 'submit',
			'value' => elgg_echo('cancel'),
			'class' => 'elgg-button elgg-button-cancel',
	));

echo elgg_view('input/form', array(
	'body' => $body,
	'enctype' => 'multipart/form-data',
	'action' => $vars['url'] . 'action/dropbox/upload',
	'method' => 'post',
	'class' => 'dropbox-actions'));