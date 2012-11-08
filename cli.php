<?php

require_once __DIR__ . '/vendor/autoload.php';

$action = $argv[1];
$project_dir = $argv[2];

$deploy = new Thoom\Giply($project_dir);
$deploy->$action();

readfile($project_dir . '/deployments.log');