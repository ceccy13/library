<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */
 
$current_dir_path = getcwd();
$iterator =new DirectoryIterator(($current_dir_path));

foreach($iterator as $fileInfo) {
	if($fileInfo->isDot() || $fileInfo == 'js' || $fileInfo == 'css') continue;
    if($fileInfo->isDir()) {
	    $dir_project = $fileInfo;
		break;
    }
}

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/'.$dir_project.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/'.$dir_project.'/public/index.php';
