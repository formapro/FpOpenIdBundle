# Overview

Integrates OpenId feature to symfony's security layer.
Internally uses [LightOpenID](http://gitorious.org/lightopenid)

# Get started

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

# Manual

## Request for additional parameters:

* First you need to add some options:

        fp_open_id:
            provider:
                options_required: [contact/email]
                options_optional: [namePerson, namePerson/first]

* After success registration you can fetch them from the token:

        $token->getAttribute('contact/email');
        $token->getAttribute('namePerson/first');

## Storing to db

* Define a route for post auth operations:

        fp_open_id:
            provider:
                approve_route:     'openid_approve_user'
                options_required: [contact/email]

* Create an action which take care of the job:

        public function approveUserAccount($request)
        {
            $tokenPersister = $this->get('security.authentication.token_persister');

            $token = $tokenPersister->get();
            $user = $this->get('user.repository')->findBy(array('email' => $token->getAttribute('contact/email')));

            $newToken = new OpenIdToken($token->getOpenIdentifier(), $user->getRoles());
            $newToken->setUser($user);

            $tokenPersister->set($newToken);

            return $this->redirect($this->generateUrl('login_check_route', array('openid_approved' => 1)));
        }

    That's IMPORTANT to now about post auth actions:
    * It is required to set a user to token (UserInterface)
    * It is required make a redirect to `login_check` with parameter `openid_approved`

