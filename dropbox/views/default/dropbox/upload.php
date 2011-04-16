<?php

$path = $vars['path'];

$body .= '<div class="contentWrapper">';

$body .= elgg_view('input/hidden', array(
			'internalname' => 'path',
			'value' => $path,
		));

$body .= sprintf(elgg_echo('dropbox:upload:info'), $path);

$body .= elgg_view('input/file', array(
			'internalname' => 'file',
		));

$body .= elgg_view('input/submit', array(
			'internalname' => 'submit',
			'value' => elgg_echo('upload'),
		));

$body .= elgg_view('input/submit', array(
			'internalname' => 'submit',
			'value' => elgg_echo('cancel'),
		));

$body .= '</div>';

echo elgg_view('input/form', array(
			'body' => $body,
			'enctype' => 'multipart/form-data',
			'action' => $vars['url'] . 'action/dropbox/upload',
			'method' => 'post'));