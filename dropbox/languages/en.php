<?php
/**
 * Dropbox widget language file
 */

$english = array(
	'dropbox:title' => 'Dropbox',
	'dropbox:info' => 'Manage your Dropbox and share files',
	'dropbox:username' => 'Enter your Dropbox username.',
	'dropbox:info' => 'Informations',
	'dropbox:root' => 'Your directory',
	'dropbox:user' => 'Your Dropbox',
	'dropbox:upload' => 'Upload',
	'dropbox:newfolder' => 'New folder',
	'dropbox:sharefolder' => 'Share a folder',
	'dropbox:put' => 'Put a file',
	'dropbox:remove' => 'Remove a file',
	'dropbox:filename' => 'File Name',
	'dropbox:size' => 'Size',
	'dropbox:modified' => 'Modified',
	'dropbox:parent_folder' => 'Parent folder',
	'dropbox:access_key' => 'Access Key',
	'dropbox:access_secret' => 'Access Secret',
	'dropbox:consumer_key' => 'Consumer Key',
	'dropbox:consumer_secret' => 'Consumer Secret',
	'dropbox:usersettings:description' => 'Link your ' . $CONFIG->site->name . ' account with Dropbox.',
	'dropbox:account:name' => 'Name :',
	'dropbox:account:email' => 'E-Mail :',
	'dropbox:account:space:available' => 'Available space :',
	'dropbox:account:space:free' => 'Free space : ',
	'dropbox:error' => 'An error occured',
	'dropbox:error:badconsumer' => 'Bad consumer, contact your administrator.',
	'dropbox:error:missingconsumer' => 'Your administrator need to add the consumer key and secret.',
	'dropbox:error:baduserpass' => 'Bad username or password.',
	'dropbox:error:missinguserpass' => 'You may define the username and password of your Dropbox account in the plugin setting menu.',
    'dropbox:error:unknow' => 'Unknow error, contact your administrator.',
);
add_translation("en", $english);
