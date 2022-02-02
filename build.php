<?php
/*
 * Copyright (c) 2022 Jan Sohn.
 * All rights reserved.
 * I don't want anyone to use my source code without permission.
 */
declare(strict_types=1);
/**
 * Build script
 */
$IS_GITHUB_ACTIONS = (getenv("IS_GITHUB_ACTIONS") !== false);
$startTime = microtime(true);
// Input & Output directory...
$from = getcwd() . DIRECTORY_SEPARATOR;
$description = yaml_parse_file($from . "plugin.yml");
$to = __DIR__ . DIRECTORY_SEPARATOR . "out" . DIRECTORY_SEPARATOR . $description["name"] . DIRECTORY_SEPARATOR;
$outputPath = $from . "out" . DIRECTORY_SEPARATOR . $description["name"] . "_v" . $description["version"];
@mkdir($to, 0777, true);
// Clean output directory...
cleanDirectory($to);
// Copying new files...
if (is_dir($from . "src")) {
	copyDirectory($from . "src", $to . "src");
}
if (is_file($from . "LICENSE")) {
	file_put_contents($to . "LICENSE", file_get_contents($from . "LICENSE"));
}
if (is_file($from . "README.md")) {
	file_put_contents($to . "README.md", file_get_contents($from . "README.md"));
}
if (is_dir($from . "resources")) {
	copyDirectory($from . "resources", $to . "resources");
}
yaml_emit_file($to . "plugin.yml", $description);
// Defining output path...
@unlink($outputPath . ".phar");
if($IS_GITHUB_ACTIONS){
	if (file_exists($API = $from . "out" . DIRECTORY_SEPARATOR . ".API.txt")) {
		unlink($API);
	}
	if (file_exists($VERSION = $from . "out" . DIRECTORY_SEPARATOR . ".VERSION.txt")) {
		unlink($VERSION);
	}
	if (file_exists($FILE_NAME = $from . "out" . DIRECTORY_SEPARATOR . ".FILE_NAME.txt")) {
		unlink($FILE_NAME);
	}
	if (file_exists($FOLDER = $from . "out" . DIRECTORY_SEPARATOR . ".FOLDER.txt")) {
		unlink($FOLDER);
	}
	file_put_contents($API, (is_array($description["api"]) ? explode(".", $description["api"][0])[0] : (is_string($description["api"]) ? explode(".", $description["api"])[0] : "???")), 0777);
	file_put_contents($VERSION, $description["version"], 0777);
	file_put_contents($FILE_NAME, $outputPath, 0777);
	file_put_contents($FOLDER, $to, 0777);
}
// Generate phar
$phar = new Phar($outputPath . ".phar");
$phar->buildFromDirectory($to);
$phar->addFile(__DIR__ . DIRECTORY_SEPARATOR . "LICENSE");
$phar->addFile(__DIR__ . DIRECTORY_SEPARATOR . "README.md");
$phar->compressFiles(Phar::GZ);
printf("Built in %s seconds! Output path: %s\n", round(microtime(true) - $startTime, 3), $outputPath);
if (is_dir("C:/Users/kfeig/Desktop/pmmp" . (is_array($description["api"]) ? explode(".", $description["api"][0])[0] : (is_string($description["api"]) ? explode(".", $description["api"])[0] : "???")) . "/plugins")) {
	// Defining output path...
	$outputPath = "C:/Users/kfeig/Desktop/pmmp" . (is_array($description["api"]) ? explode(".", $description["api"][0])[0] : (is_string($description["api"]) ? explode(".", $description["api"])[0] : "???")) . "/plugins" . DIRECTORY_SEPARATOR . $description["name"] . "_v" . $description["version"];
	@unlink($outputPath . ".phar");
	// Generate phar
	$phar = new Phar($outputPath . ".phar");
	$error = true;
	while ($error) {
		try {
			$phar->buildFromDirectory($to);
			$error = false;
		} catch (PharException $e) {
		}
		if ($error) {
			echo "Cannot access to file, file is used" . PHP_EOL;
			sleep(2);
		}
	}
	$phar->addFile(__DIR__ . DIRECTORY_SEPARATOR . "LICENSE");
	$phar->addFile(__DIR__ . DIRECTORY_SEPARATOR . "README.md");
	$phar->compressFiles(Phar::GZ);
	printf("Built in %s seconds! Output path: %s\n", round(microtime(true) - $startTime, 3), $outputPath . ".phar");
}
# Functions:
function copyDirectory(string $from, string $to, array $ignoredFiles = []): void{
	@mkdir($to, 0777, true);
	$ignoredFiles = array_map(fn(string $path) => str_replace("/", "\\", $path), $ignoredFiles);
	$files = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator(new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS), function (SplFileInfo $fileInfo, $key, $iterator) use ($from, $ignoredFiles): bool{
		if (!empty($ignoredFiles)) {
			$path = str_replace("/", "\\", $fileInfo->getPathname());
			foreach ($ignoredFiles as $ignoredFile) {
				if (str_starts_with($path, $ignoredFile)) {
					return false;
				}
			}
		}
		return true;
	}), RecursiveIteratorIterator::SELF_FIRST);
	/** @var SplFileInfo $fileInfo */
	foreach ($files as $fileInfo) {
		$target = str_replace($from, $to, $fileInfo->getPathname());
		if ($fileInfo->isDir()) {
			@mkdir($target, 0777, true);
		} else {
			$contents = file_get_contents($fileInfo->getPathname());
			file_put_contents($target, $contents);
		}
	}
}

/**
 * Function cleanDirectory
 * @param string $directory
 * @return void
 */
function cleanDirectory(string $directory): void{
	$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
	/** @var SplFileInfo $fileInfo */
	foreach ($files as $fileInfo) {
		if ($fileInfo->isDir()) {
			rmdir($fileInfo->getPathname());
		} else {
			unlink($fileInfo->getPathname());
		}
	}
}