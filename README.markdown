# Overview

Integrates OpenId feature to symfony's security layer.
Supports these 3rd party libraries:

* [LightOpenID](http://gitorious.org/lightopenid)

# Get started

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

* Configure the bundle

        fp_open_id:
            provider:
                return_route:             'login_check_route'
                roles:                    [ROLE_USER]

           consumers:
                light_open_id:
                    trust_root:           'example.com'

* Configure security bundle

        security:
            factories:
                -                         /path/to/vendor/bundles/Fp/OpenIdBundle/Resources/config/security_factories.xml

            firewalls:
                secured_area:
                    pattern:              ^/
                    anonymous:            ~
                    logout:
                        path:             /logout
                        target:           /
                    openid:               true

* Render login form

        {% render "FpOpenIdBundle:OpenId:loginForm" %}

* Try it with:

        https://www.google.com/accounts/o8/id

# Manual

## Request for additional parameters:

* Define options you want to request:

        fp_open_id:
            consumers:
                light_open_id
                    required:             [ contact/email ]
                    optional:             [ namePerson, namePerson/first ]

* After success registration you can fetch them from the token:

        $token->getAttribute('contact/email');
        $token->getAttribute('namePerson/first');

## Set a fixed OpenID identity (e.g. Google Apps domain)

        fp_open_id:
            consumers:
                light_open_id
                    identifier:           'http://example.com/openid'
                    required:             [ contact/email ]
                    optional:             [ namePerson, namePerson/first ]

## Post auth action

* Define a route for post auth operations:

        fp_open_id:
            provider:
                approve_route:            'openid_approve_user'

            consumers:
                light_open_id
                    required:             [ contact/email ]

* Create an action which do post auth job:

        public function approveUserAccount($request)
        {
            $tokenPersister = $this->get('fp_openid.security.authentication.token_persister');

            $token = $tokenPersister->get();

            $user = $this->get('user.repository')->findBy(array('email' => $token->getAttribute('contact/email')));

            // IMPORTANT: It is required to set a user to token (UserInterface)
            $newToken = new OpenIdToken($token->getIdentifier(), $user->getRoles());
            $newToken->setUser($user);

            $tokenPersister->set($newToken);

            // IMPORTANT: It is required make a redirect to `login_check` with parameter `openid_approved`
            return $this->redirect($this->generateUrl('login_check_route', array('openid_approved' => 1)));
        }
