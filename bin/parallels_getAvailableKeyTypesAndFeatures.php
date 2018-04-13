#!/usr/bin/env php
<?php

require_once __DIR__.'/../../../../include/functions.inc.php';
//$GLOBALS['tf']->session->create(160308,'services');
//$GLOBALS['tf']->session->verify();

$ka = new \Detain\Parallels\Parallels();
print_r($ka->getAvailableKeyTypesAndFeatures());

//$GLOBALS['tf']->session->destroy();
