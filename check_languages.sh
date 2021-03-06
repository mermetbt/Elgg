#!/usr/bin/php
<?php

/* This is a temp array */
$lang = array();

/**
 * This function is just an overload of an existing function in elgg.
 */
function add_translation($lg, $translations) {
  global $lang;
  $lang[$lg] = $translations;
}

/**
 * @class PluginChecker This class is used to check if the language files associated
 * with a plugin contain keys which are really used by the plugin.
 */
class PluginChecker {

  var $_plugin; // Contains the name of the plugin.
  var $_lang;   // Contains the set of key => translation.
  var $_files;  // Contains the PHP files of the plugin.

  /**
   * This function return an array which contains all the php files in the directory $c_dir. 
   * 
   * @param $c_dir Current directory.
   * @param $path  Current path (set to . at the beginning).
   * @return an array containing all the PHP files.
   */
  private function getPHPfiles($c_dir, $path) {
     /* We enter in the current directory */
     chdir($c_dir);

     $path = $path.'/'.$c_dir;

     /* This var will contains all the PHP files. */
     $files = array();

     /* We open the dir to read all the files */
     if(!$dir = opendir('.')) {
       return $files;
     }
     
     while(false !== ($file = readdir($dir))) {
        /* If the file is a directory, we enter in it. */
        if(is_dir($file)) {
          if($file != 'languages' && $file != '.' && $file != '..') {
            $files = array_merge($files, $this->getPHPfiles($file, $path));
          }
        }
        else
        /* If the file is a PHP files, we add it into the list. */
        if(substr($file, -4, 4) == '.php') {
          $files[] = $path.'/'.$file;
        }
      }
      closedir($dir);

      chdir('..');

      return $files;
  }

  /**
   * This function load all the languages files from the plugin directory. 
   *
   * @param $plugin Name of the plugin (and it's dirname).
   */
  function load($plugin) {
    global $lang;
    $this->_plugin = $plugin;
    $lang = array();

    /* We check if the languages directory exists */
    if(!file_exists('./'.$plugin.'/languages')) {
      return;
    }

    /* We check if the directory is openable. */
    if(!$dir = opendir('./'.$plugin.'/languages/')) {
      return;
    }

    /* For each file in the directory, we open all file with the php extension. */
    while(false !== ($file = readdir($dir))) {
      if(!is_dir($file) && substr($file, -4, 4) == '.php') {
        include_once($plugin.'/languages/'.$file);
      }
    }
    closedir($dir);
    $this->_lang = $lang;

    $this->_files = $this->getPHPfiles($plugin, '.');

  }

  /**
   * This function check if all the keys are matched in all the PHP files of the plugins.
   */
  function check() {

    if(!empty($this->_lang)) {
      foreach($this->_lang AS $lg => $tr) {

        /* Initialize the starting state. */
        foreach($tr AS $key => $stc) {
          $matched[$key] = false;
        }

        /* In each file, we check if keys are matched. */
        foreach($this->_files AS $file) {
          $handle = fopen($file, "r");
          $size = filesize($file);

          if($size == 0) {
            echo "The file $file is empty.\n";
          }
          else {
            $contents = fread($handle, $size);  

            /* We check each key on the contents. */
            foreach($tr AS $key => $stc) {
              if(strpos($contents, "'$key'") !== false || strpos($contents, "\"$key\"") !== false) {
                $matched[$key] = true;
              }
            }
          }
          fclose($handle);
        }

        /* Finally, we print the keys unmatched. */
        foreach($tr AS $key => $stc) {
          if($matched[$key] == false)
            echo "$key from the language $lg is not found in the plugin ".$this->_plugin."\n";
        }
      }
    }
  }
}

/* Open the current directory containing all the plugins. */
$dir = opendir('.');

/* For each file in the directory, we load the plugin's language */
while(false !== ($file = readdir($dir))) {
  if($file != '.' && $file != '..' && is_dir($file)) {
    $pl = new PluginChecker();
    $pl->load($file);
    $pl->check();
  }
}
closedir($dir);

?>
