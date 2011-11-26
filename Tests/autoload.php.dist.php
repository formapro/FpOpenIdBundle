<?php

require_once __DIR__ . '/../vendor/.composer/autoload.php';

$mapLoader = new MapClassLoader(array(
    'LightOpenID' => __DIR__ . '/../vendor/fp/lightopenid/openid.php'
));
$mapLoader->register();