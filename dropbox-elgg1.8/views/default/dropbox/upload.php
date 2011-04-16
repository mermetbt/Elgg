<?php

$path = $vars['path'];

$body .= '<div class="contentWrapper">';

$body .= elgg_view('input/hidden', array(
			'name' => 'path',
			'value' => $path,
		));

$body .= sprintf(elgg_echo('dropbox:upload:info'), $path);

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

$body .= '</div>';

echo elgg_view('input/form', array(
	'body' => $body,
	'enctype' => 'multipart/form-data',
	'action' => $vars['url'] . 'action/dropbox/upload',
	'method' => 'post'));