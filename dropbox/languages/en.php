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
	'dropbox:newfolder' => 'New folder',
	'dropbox:sharefolder' => 'Share a folder',
	'dropbox:put' => 'Put a file',
	'dropbox:remove' => 'Remove a file',
	'dropbox:share' => 'Share',
	'dropbox:deleted' => 'File(s) deleted.',
	'dropbox:folder:created' => 'Folder %s created.',
	'dropbox:folder:new:info' => 'Enter the name of the folder that you want at %s',
	'dropbox:folder:share:info' => 'Share a folder with your friends.',
	'dropbox:folder:share:new' => "I'd like to create and share a new folder",
	'dropbox:folder:share:existing' => "I'd like to share an existing folder",
	'dropbox:filename' => 'File Name',
	'dropbox:size' => 'Size',
	'dropbox:modified' => 'Modified',
	'dropbox:parent_folder' => 'Parent folder',
	'dropbox:access_key' => 'Access Key',
	'dropbox:access_secret' => 'Access Secret',
	'dropbox:consumer_key' => 'Consumer Key',
	'dropbox:consumer_secret' => 'Consumer Secret',
	'dropbox:usersettings:userpass' => 'Enter your username and password.',
	'dropbox:usersettings:linked' => 'Your account is linked.',
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
	'dropbox:error:forbidden' => 'Forbidden: This could mean a bad username or password, or a file or folder already existing at the target location.',
    'dropbox:error:unknow' => 'Unknow error, contact your administrator.',
	'dropbox:error:nofilesselected' => 'Error : no file selected.',

);
add_translation("en", $english);
