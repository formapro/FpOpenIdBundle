# Overview [![Build Status](https://secure.travis-ci.org/formapro/FpOpenIdBundle.png?branch=master)](http://travis-ci.org/formapro/FpOpenIdBundle)

Integrates OpenId feature to symfony's security layer.
Supports these 3rd party libraries:

* [LightOpenID](http://gitorious.org/lightopenid)

# Get started

**The master branch does not supports symfony 2.0 please use branch [1.0](https://github.com/formapro/FpOpenIdBundle/tree/1.0).**

* Setup [LightOpenId](http://gitorious.org/lightopenid)

```
git submodule add git://gitorious.org/lightopenid/lightopenid.git /path/to/vendor/LightOpenId
```

* Setup Bundle

```
git submodule add git@github.com:formapro/FpOpenIdBundle.git /path/to/vendor/bundles/Fp/OpenIdBundle
```

* Configure autioload.php

```php
<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;
use Symfony\Component\ClassLoader\MapClassLoader;

$universalLoader = new UniversalClassLoader;
$universalLoader->registerNamespaces(array(
    'Fp' => '/path/to/vendor/bundles'
));

$universalLoader->register();

$mapLoader = new MapClassLoader(array(
    'LightOpenID' => '/path/to/venodr/LightOpenId/openid.php'
));

$mapLoader->register();
```

* Configure AppKernel.php

```php
<?php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Fp\OpenIdBundle\FpOpenIdBundle()
        );
    }
}
```

* Full openid firewall configuration

```yml

firewalls:
    secured_area:
        pattern:              ^/
        anonymous:            ~
        logout:
            path:             /logout
            target:           /
        fp_openid:
            relying_party:                  fp_openid.relying_party.default
            required_parameters:
                - contact/email
                - namePerson/first
            optional_parameters:
                - namePerson/last
            required_parameters:
            check_path:                     /login_check
            login_path:                     /login',
            always_use_default_target_path: false
            default_target_path:            /
            target_path_parameter:          _target_path
            use_referer:                    false
            failure_path:                   null
            failure_forward:                false

```

* Try it with google:

```
https://www.google.com/accounts/o8/id
```

* Getting user information from openid provider:

**Pay attention to that fact that an openid provider is not required to return any data, you can get nothing even if you set it required**

Request parameters:

```yml

firewalls:
    secured_area:
        fp_openid:
            required_parameters:
                - contact/email
                - namePerson/first
            optional_parameters:
                - namePerson/last
```

Get them from token:

```php
<?php

/**
 * @var $securityContext \Symfony\Component\Security\Core\SecurityContextInterface
 */
$attributes = $securityContext->getToken()->getAttributes();

if (isset($attributes['contact/email'])) {
    echo $attributes['contact/email'];
}
```

* Secure pages only for openid logged in users:

```yml
    access_control:
        - { path: ^/demo/secured/openid, role: IS_AUTHENTICATED_OPENID }
```