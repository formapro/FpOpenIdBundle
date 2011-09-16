# Overview

Integrates OpenId feature to syfony's security layer.
Internally uses [LightOpenID](http://gitorious.org/lightopenid)


# Manual

* Setup [LightOpenId](http://gitorious.org/lightopenid)
* Setup Bundle
* Configure autioload.php

        use Symfony\Component\ClassLoader\UniversalClassLoader;
        use Symfony\Component\ClassLoader\MapClassLoader;

        $universalLoader = new UniversalClassLoader;
        $universalLoader->registerNamespaces(array(
            'Fp' => '/Path/To/Bundle'
        ));

        $universalLoader->register();

        $mapLoader = new MapClassLoader(array(
            'LightOpenID' => '/Path/To/LightOpenId'
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

* Configure the bundle

        fp_open_id:
            provider:
                return_route:     'login_check_route'
                roles:            [ROLE_USER]

           light_open_id:
                trust_root:       'example.com'

* Configure security bundle

        security:
            factories:
                -                 /Path/To/Fp/OpenIdBundle/Resources/config/security_factories.xml

            firewalls:
                secured_area:
                pattern:              ^/
                anonymous:            ~
                logout:
                    path:             /logout
                    target:           /
                openid:
                    login_path:       /login
                    check_path:       /login_check

* Render simple form

        {% render "FpOpenIdBundle:OpenId:simpleForm" %}

* Request for additional parameters:

        fp_open_id:
            provider:
                return_route:     'login_check_route'
                roles:            [ROLE_USER]
                options_required: [contact/email]
                options_optional: [namePerson, namePerson/first]

           light_open_id:
                trust_root:       'example.com'

   Fetch them from the token:

        $token->getAttribute('contact/email');