<?php

/**
 * This class is used to check if the language files associated
 * with a plugin contain keys which are really used by the plugin.
 *
 * @package Tools
 * @class LanguageChecker
 */
class LanguageChecker {

	var $_plugin;		  // Contains the name of the plugin.
	var $_translations;   // Contains the set of key => translation.
	var $_files;		  // Contains the PHP files of the plugin.

	/**
	 * This function return an array which contains all the php files in the directory $c_dir.
	 *
	 * @param string $c_dir Current directory.
	 * @param string $path  Current path (set to . at the beginning).
	 * @return an array containing all the PHP files.
	 */
	private function getPhpFiles($c_dir, $path) {
		$path = ltrim($path) . '/' . $c_dir;

		/* We enter in the current directory */
		chdir($path);

		/* This var will contains all the PHP files. */
		$files = array();

		/* We open the dir to read all the files */
		if (!$dir = opendir('.')) {
			return $files;
		}

		while (false !== ($file = readdir($dir))) {
			/* If the file is a directory, we enter in it. */
			if (is_dir($file)) {
				if ($file != 'languages' && $file[0] != '.') {
					$files = array_merge($files, $this->getPhpFiles($file, $path));
				}
			} else {
				/* If the file is a PHP files, we add it into the list. */
				if (substr($file, -4, 4) == '.php') {
					$files[] = $path . '/' . $file;
				}
			}
		}
		closedir($dir);

		chdir('..');

		return $files;
	}

	/**
	 * This function load all the languages files from the plugin directory.
	 *
	 * @param string $plugin Name of the plugin (and it's dirname).
	 * @return true|false
	 */
	function load($plugin) {
		$this->_plugin = $plugin;
		global $CONFIG;

		/* Save the current state of translations. */
		$lang = $CONFIG->translations;

		/* Flush the current translations. */
		$CONFIG->translations = array();

		/* Get the languages path */
		$path = elgg_get_plugins_path() . $plugin . '/languages/';

		/* We check if the languages directory exists */
		if (!file_exists($path)) {
			$CONFIG->translations = $lang;
			return false;
		}

		/* We check if the directory is openable. */
		if (!$dir = opendir($path)) {
			$CONFIG->translations = $lang;
			return false;
		}

		/* For each file in the directory, we open all file with the php extension. */
		while (false !== ($file = readdir($dir))) {
			if ($file[0] != '.' && !is_dir($file) && substr($file, -4, 4) == '.php') {
				include($path . $file);
			}
		}
		closedir($dir);
		$this->_translations = $CONFIG->translations;

		/* Restore translations */
		$CONFIG->translations = $lang;

		$this->_files = $this->getPhpFiles($plugin, elgg_get_plugins_path());
		return true;
	}

	/**
	 * This function check if all the keys are matched in all the PHP files of
	 * the plugins.
	 *
	 * @return null
	 */
	function check() {
		$unmatched = array();
		if (!empty($this->_translations)) {
			foreach ($this->_translations AS $lg => $tr) {

				/* Initialize the starting state. */
				foreach ($tr AS $key => $stc) {
					$matched[$key] = false;
				}

				/* In each file, we check if keys are matched. */
				foreach ($this->_files AS $file) {
					$handle = fopen($file, "r");
					$size = filesize($file);

					if ($size == 0) {
						echo "The file $file is empty.<br>\n";
					} else {
						$contents = fread($handle, $size);

						/* We check each key on the contents. */
						foreach ($tr AS $key => $stc) {
							if (strpos($contents, "'$key'") !== false || strpos($contents, "\"$key\"") !== false) {
								$matched[$key] = true;
							}
						}
					}
					fclose($handle);
				}

				/* Finally, we get the unmatched keys. */
				$unmatched[$lg] = array();
				foreach ($tr AS $key => $stc) {
					if ($matched[$key] == false) {
						$unmatched[$lg][] = $key;
					}
				}
			}
		}
		return $unmatched;
	}
}
