#!/usr/bin/php
<?php

require_once(__DIR__.'/../../../../include/functions.inc.php');
//$GLOBALS['tf']->session->create(160308,'services');
//$GLOBALS['tf']->session->verify();

$key = 'PLSK.03117920.0000';
$ipAddress = '206.72.205.242';
$ka = new \Detain\Parallels\Parallels();
print_r($ka->getKeyInfo($key));

//$GLOBALS['tf']->session->destroy();
