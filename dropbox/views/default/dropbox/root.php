<?php

$dropbox = $CONFIG->dropbox;

/* Just a little test to try folder creation and deletion.
$dropbox->createFolder('tmp');

try {
  $dropbox->delete('tmp');
} catch(Dropbox_Exception_NotFound $e) {
	echo '<b>', 'Directory not found', '</b>';
}
*/

/* Get listing of the root directory. */
$files = $dropbox->getLinks('');

$contents = $files['contents'];

echo '<div class="contentWrapper">';

/* Create the 'upload' button. */
$upload_button = elgg_view('input/submit', array(
			'internalname' => 'params[upload]',
			'value' => elgg_echo('dropbox:upload'),
			'class' => 'upload_button',
		));

/* Create the 'new folder' button. */
$mkdir_button = elgg_view('input/submit', array(
			'internalname' => 'params[mkdir]',
			'value' => elgg_echo('dropbox:newfolder'),
			'class' => 'mkdir_button',
		));

/* Create the 'share a folder' button. */
$share_button = elgg_view('input/submit', array(
			'internalname' => 'params[share]',
			'value' => elgg_echo('dropbox:sharefolder'),
			'class' => 'share_button',
		));

/* Print the buttons. */
echo '<div class="dropbox_buttons">';
echo $upload_button, $mkdir_button, $share_button;
echo '</div>';

/* Print the table header of the filelist. */
echo '<table class="dropbox-list">', '<thead>';
echo '<th>', elgg_echo('dropbox:filename'), '</th>';
echo '<th>', elgg_echo('dropbox:size'), '</th>';
echo '<th>', elgg_echo('dropbox:modified'), '</th>';
echo '</thead>';

/* List all files from the Dropbox directory. */
foreach ($contents AS $file) {
	echo '<tr>';
	$checkbox = elgg_view('input/checkboxes', array(
				'internalname' => 'params[selected_files]',
				'options' => array(substr($file['path'], 1) => $file['path']),
				'class' => 'checkbox',
			));
	echo '<td>', $checkbox, '</td>';
	echo '<td>', $file['size'], '</td>';
	echo '<td>', $file['modified'], '</td>';
	echo '</tr>';
}

echo '</table>';
echo '</div>';