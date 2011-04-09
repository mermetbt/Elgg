<?php

$dropbox = $CONFIG->dropbox;

/* Get listing of the root directory. */
$files = $dropbox->getLinks('');

echo '<pre>';
print_r($files);
echo '</pre>';
