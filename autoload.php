<?php
spl_autoload_register(function($class){
	// length of prefix
	$len = 7;

	// does the class use the namespace prefix?
	if (strncmp('Sudoku\\', $class, $len)!==0){
		// no, move to the next registered autoloader
		return;
	}

	// get the relative class name
	$relative_class = substr($class, $len);

	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file = __DIR__.'/Sudoku/'.str_replace('\\', '/', $relative_class).'.php';

	// if the file exists, require it
	if (file_exists($file)){
		require $file;
	}
});

require __DIR__.'/vendor/autoload.php'; // composer
