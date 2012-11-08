<?php
/**
 * self-update.php
 *
 * Creates environment needed to set up the Giply server
 *
 * @author Z. d. Peacock <zdp@thoomtech.com>
 * @copyright (c) 2012 Thoom Technologies LLC
 *
 * @since 6/26/12 7:24 PM
 */

$parent_dir = dirname(__DIR__);
$composer = __DIR__ . '/composer.phar';
$lock = __DIR__ . '/composer.lock';
$config = __DIR__ . '/giply_config.json';

while (!file_exists($composer)) {
    file_put_contents($composer, file_get_contents("http://getcomposer.org/installer"));
}

passthru("git reset --hard HEAD");
passthru("git pull origin master");

passthru("php $composer install --prefer-dist -o");

$json = array();
if (file_exists($config)) {
    $json = json_decode(file_get_contents($config), true);
}

$to_config = array_merge(array('parent_dir' => $parent_dir), $json);

file_put_contents($config, json_encode($to_config));

