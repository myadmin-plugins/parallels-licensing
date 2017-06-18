#!/usr/bin/php
<?php

require_once(__DIR__.'/../../../include/functions.inc.php');
//$GLOBALS['tf']->session->create(160308,'services');
//$GLOBALS['tf']->session->verify();

$Key = 'PLSK.03117920.0000';
$ip = '206.72.205.242';
$ka = new \Detain\Parallels\Parallels();
print_r($ka->getAvailableUpgrades($Key));

//$GLOBALS['tf']->session->destroy();
