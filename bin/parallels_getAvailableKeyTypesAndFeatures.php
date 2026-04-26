#!/usr/bin/env php
<?php

require_once __DIR__.'/../../../../include/functions.inc.php';
//\MyAdmin\App::session()->create(160308,'services');
//\MyAdmin\App::session()->verify();

$ka = new \Detain\Parallels\Parallels();
print_r($ka->getAvailableKeyTypesAndFeatures());

//\MyAdmin\App::session()->destroy();
