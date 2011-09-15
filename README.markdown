# Overview

Integrates OpenId feature to syfony's security layer.
Internally uses [LightOpenID](http://gitorious.org/lightopenid)


# Manual

* Setup as submodule
* Configure autioload.php
* Configure AppKernel.php
* Configure the bundle

        fp_open_id:
            provider:
                return_route:     'login_check_route'
                roles:            [ROLE_USER]
                options_required: [contact/email]
                options_optional: [namePerson, namePerson/first]

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