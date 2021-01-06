<?php
spl_autoload_register(function($className) {
	$file = dirname(__DIR__) . '\\curl' . '\\src\\' . $className . '.php';
	$file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
	//echo $file;
	if (file_exists($file)) {
		include $file;
	}
});
?>