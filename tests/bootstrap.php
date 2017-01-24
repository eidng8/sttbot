<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-12-08
 * Time: 22:25
 */
$dir = dirname($filename);
$cache = "$dir/data/cache";

// exec("rm -f $cache/exptmpls/*");

//$cmd = "find $cache/ -exec touch {} \\;";
// echo $cmd;
//exec($cmd);
$di = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(
        $cache,
        FilesystemIterator::CURRENT_AS_PATHNAME
    )
);
foreach ($di as $file) {
    touch($file);
}

require __DIR__ . '/../vendor/autoload.php';
