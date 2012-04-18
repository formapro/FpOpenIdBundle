Install as git submodules
=========================

### Step 1. Add submodules

``` bash
$ git submodule add git://github.com/formapro/LightOpenID.git vendor/fp/lightopenid
$ git submodule add git://github.com/formapro/FpOpenIdBundle.git vendor/fp/openid-bundle/Fp/OpenIdBundle
$ git submodule update --init
```

### Step 2: Configure the Autoloader

You have to add the `Fp` namespace and `LightOpenId` class to your autoloader:

``` php
<?php
// app/autoload.php

//require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/ClassLoader/MapClassLoader.php';

//uncomment this if you dont have universal class loader instance.
//$loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();

$loader->registerNamespaces(array(
    // ...
    'Fp' => __DIR__.'/../vendor/fp/openid-bundle',
));

$mapClassLoader = new \Symfony\Component\ClassLoader\MapClassLoader(array(
    'LightOpenID' => __DIR__ . '/../vendor/fp/lightopenid/openid.php'
));

$loader->register();
$mapClassLoader->register();

```