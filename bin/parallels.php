#!/usr/bin/env php
<?php

require_once __DIR__.'/../../../../include/functions.inc.php';
//\MyAdmin\App::session()->create(160308,'services');
//\MyAdmin\App::session()->verify();

$key = 'PLSK.03117920.0000';
$ipAddress = '206.72.205.242';
$ka = new \Detain\Parallels\Parallels();
print_r($ka->getKeyInfo($key));
//print_r($ka->getKeyNumbers(array($ipAddress)));
//print_r($ka->getAvailableKeyTypesAndFeatures());
//print_r($ka->getAvailableUpgrades($key));
//print_r($ka->retrieveKey($key));


//\MyAdmin\App::session()->destroy();
