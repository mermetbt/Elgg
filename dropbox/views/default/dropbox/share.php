<?php

$path = $vars['path'];

echo '<div class="contentWrapper">';

/* Add path parameter to the form */
$body = elgg_view('input/hidden', array(
			'internalname' => 'path',
			'value' => $path,
		));

$body .= '<div class="contentWrapper">';
if(empty($path)) {
	$path = '/';
}

$body .= sprintf(elgg_echo('dropbox:folder:share:info'), $path) . '<br>';


$body .= elgg_view('input/radio', array(
			'internalname' => 'choice',
			'value' => '1',
			'options' => array(elgg_echo('dropbox:folder:share:new') => '1'),
		));

$body .= elgg_view('input/text', array(
			'internalname' => 'name',
			'value' => '',
		));

$body .= elgg_view('input/radio', array(
			'internalname' => 'choice',
			'options' => array(elgg_echo('dropbox:folder:share:existing') => '2'),
		));

$body .= '</div>';
$body .= '<div class="contentWrapper">';

$body .= elgg_echo('dropbox:folder:share:selectusers');

$body .= elgg_view('input/userpicker', array(
			'internalname' => 'selecteduser',
		));

$body .= elgg_view('input/submit', array(
			'internalname' => 'submit',
			'value' => elgg_echo('dropbox:share'),
		));

$body .= elgg_view('input/submit', array(
			'internalname' => 'submit',
			'value' => elgg_echo('cancel'),
		));

$body .= '</div>';

echo elgg_view('input/form', array(
			'body' => $body,
			'action' => $vars['url'] . 'action/dropbox/share',
			'method' => 'post'));

