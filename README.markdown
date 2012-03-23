# Overview

Integrates OpenId feature to symfony's security layer.
Supports these 3rd party libraries:

* [LightOpenID](http://gitorious.org/lightopenid)

# Get started

**The master branch does not supports symfony 2.0 please use branch [1.0](https://github.com/formapro/FpOpenIdBundle/tree/1.0).**

* Setup [LightOpenId](http://gitorious.org/lightopenid)

        git submodule add git://gitorious.org/lightopenid/lightopenid.git /path/to/vendor/LightOpenId

* Setup Bundle

        git submodule add git@github.com:formapro/FpOpenIdBundle.git /path/to/vendor/bundles/Fp/OpenIdBundle

* Configure autioload.php

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

* Configure AppKernel.php

        class AppKernel extends Kernel
        {
            public function registerBundles()
            {
                $bundles = array(
                    new Fp\OpenIdBundle\FpOpenIdBundle()
                );
            }
        }

* Configure security bundle

            firewalls:
                secured_area:
                    pattern:              ^/
                    anonymous:            ~
                    logout:
                        path:             /logout
                        target:           /
                    fp_openid:
                        client:                         fp_openid.client.default
                        roles:                          [ ROLE_USER ]
                        check_path:                     /login_check
                        login_path:                     /login',
                        always_use_default_target_path: false
                        default_target_path:            /
                        target_path_parameter:          _target_path
                        use_referer:                    false
                        failure_path:                   null
                        failure_forward:                false

* Try it with:

        https://www.google.com/accounts/o8/id