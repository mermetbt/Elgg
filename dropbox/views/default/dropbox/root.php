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

/* Get the directory in args. */
$path = $vars['path'];
if(!isset($path))
  $path = '';

/* Get listing of the root directory. */
$files = $dropbox->getLinks($path);

/* Get only the contents informations and sort to obtain dir on the top. */
$contents = array_sort($files['contents'], 'is_dir');

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

$checkbox = elgg_view('input/checkboxes', array(
			'internalname' => 'selectall',
			'options' => array('' => 'all'),
			'class' => 'checkbox',
		));

echo '<th class="selector">', $checkbox, '</th>';
echo '<th class="filename">', elgg_echo('dropbox:filename'), '</th>';
echo '<th class="size">', elgg_echo('dropbox:size'), '</th>';
echo '<th class="modified">', elgg_echo('dropbox:modified'), '</th>';
echo '</thead>';

if(!empty($files['path']) || empty($files)) {
	echo '<tr>';

	/* Remove the last directory in the path. */
	$sub_path = substr($path, 1);
	$reduced_path = substr($path, 0, -strlen(strchr($sub_path, '/')));

	/* Create the link to the change the Dropbox directory. */
	$url = elgg_http_add_url_query_elements($_SERVER['REQUEST_URI'], array('path' => $reduced_path));
	$link = '<a href="'. $url .'">' . elgg_echo('dropbox:parent_folder') . '</a>';

	echo '<td class="selector"></td>';
	echo '<td class="filename">', $link, '</td>';
	echo '<td class="size"></td>';
	echo '<td class="modified"></td>';
	echo '</tr>';
}

/* List all files from the Dropbox directory. */
foreach ($contents AS $file) {
	echo '<tr>';

	/* Remove the / at the begining of the filename. */
	$filename = substr($file['path'], 1+strlen($path));


	/* Create the link to the change the Dropbox directory. */
	$url = elgg_http_add_url_query_elements($_SERVER['REQUEST_URI'], array('path' => $file['path']));
	$link = '<a href="'. $url .'">' . $filename . '</a>';
	
	$checkbox = elgg_view('input/checkboxes', array(
				'internalname' => 'params[selected_files]',
				'options' => array('' => $file['path']),
				'class' => 'checkbox',
			));
	if($file['is_dir'] == 1) {
		$size = '';
		$modified = '';
	} else {
	    $size = $file['size'];
		$modified = $file['modified'];
	}

	echo '<td class="selector">', $checkbox,'</td>';
	echo '<td class="filename">', $link, '</td>';
	echo '<td class="size">', $size, '</td>';
	echo '<td class="modified">', $modified, '</td>';
	echo '</tr>';
}

echo '</table>';
//echo '<pre>';
//print_r($files);
//echo '</pre>';
echo '</div>';