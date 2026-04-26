#!/usr/bin/env php
<?php

require_once __DIR__.'/../../../../include/functions.inc.php';
//\MyAdmin\App::session()->create(160308,'services');
//\MyAdmin\App::session()->verify();

$key = $_SERVER['argv'][1];
$ipAddress = '206.72.205.242';
$ka = new \Detain\Parallels\Parallels();
print_r($ka->getKeyInfo($key));

//\MyAdmin\App::session()->destroy();
