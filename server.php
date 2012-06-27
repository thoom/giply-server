<?php
/**
 * server.php
 *
 * @author Z. d. Peacock <zdp@thoomtech.com>
 * @copyright (c) 2012 Thoom Technologies LLC
 * @since 6/26/12 7:32 PM
 */

if (!file_exists(__DIR__ . '/giply_config.json')) {
    header("500 config missing", true, 500);
    exit("Configuration file missing");
}

$config = json_decode(file_get_contents(__DIR__ . '/giply_config.json'), true);

$action = $project = $hash = null;
list($action, $project, $hash) = explode('/', substr($_SERVER['REQUEST_URI'], 1));

if (!in_array($action, array('pull', 'self-update'))) {
    header("400 Invalid action", true, 400);
    exit('Missing action');
}

if ($action == 'self-update') {
    include __DIR__ . '/self-update.php';
    exit ('OK');
}

require_once __DIR__ . '/vendor/autoload.php';
if (!$project) {
    header("400 Missing project", true, 400);
    exit('Missing project to pull');
}

if (!$hash) {
    header("400 Missing hash", true, 400);
    exit('Missing security hash');
}

if (!isset($_POST['payload'])) {
    header("Missing POST payload", true, 400);
    exit("Missing POST payload");
}

$project_dir = $config['parent_dir'] . "/$project";
if ($hash != md5($project_dir)) {
    header("400 Invalid hash", true, 400);
    exit('Invalid security hash');
}

if (!is_dir("$project_dir/.git")) {
    header("400 Invalid project name", true, 400);
    exit('Invalid project name');
}

$lock = "$project_dir/giply_process.lock";

set_time_limit(120);
$timeout = 60;
$i = 0;
while (file_exists($lock)) {
    if (md5($_POST['payload']) == file_get_contents($lock))
        exit();

    sleep(2);
    $i += 2;

    if ($i == $timeout) {
        error_log('Giply timeout.');
        exit();
    }
}

register_shutdown_function(function() use ($lock)
{
    unlink($lock);
});

file_put_contents($lock, md5($_POST['payload']));
file_put_contents("$project_dir/giply_payload.json", $_POST['payload']);

$deploy = new Thoom\Giply($project_dir);
$deploy->log("Payload: " . $_POST['payload'], Thoom\Giply::LOG_DEBUG);

$deploy->$action();