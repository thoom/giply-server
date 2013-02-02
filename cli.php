<?php

require_once __DIR__ . '/vendor/autoload.php';

$action      = $argv[1];
$project_dir = $argv[2];

if (!is_dir($project_dir)) {
    $project_dir = dirname(__DIR__) . "/$project_dir";
}

if (!is_dir($project_dir)) {
    exit("Invalid project directory\n");
}

if (!is_dir($project_dir . "/.git")) {
    exit("Missing required Git repository\n");
}

$deploy = new Thoom\Giply($project_dir);
$deploy->$action();

readfile($project_dir . '/deployments.log');