Getting Started With FpOpenIdBundle
===================================

## Installation

### Step 1. Download FpOpenIdBundle

Ultimately, the FpOpenIdBundle files should be downloaded to the
`vendor/bundles/Fp/OpenIdBundle` directory.

This can be done in several ways, depending on your preference. The first
method is the standard method for Symfony 2.1

**Using composer**

Add the following lines in your `composer.json` file:

```json
{
    "require": {
        "fp/openid-bundle": "dev-master"
    }
}
```

Now, run composer.phar to download the bundle:

```bash
$ php composer.phar install
```

**Using submodules**

If you prefer instead to use git submodules, then run the following:

``` bash
$ git submodule add git://github.com/formapro/LightOpenID.git vendor/fp/lightopenid
$ git submodule add git://github.com/formapro/FpOpenIdBundle.git vendor/fp/openid-bundle/Fp/OpenIdBundle
$ git submodule update --init
```

### Step 2: Configure the Autoloader

**Note:** You can skip this step if you use composer. Since it generates auto loading files for you.

Let's assume you add sub modules to `vendor` directory. So you have to add the `Fp` namespace and `LightOpenId` class to your autoloader:

``` php
<?php
// app/autoload.php

require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerNamespaces(array(
    // ...
    'Fp' => __DIR__.'/../vendor/fp/openid-bundle',
));

$loader->register();

require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/ClassLoader/MapClassLoader.php';
use Symfony\Component\ClassLoader\MapClassLoader;

$mapClassLoader = new MapClassLoader(array(
    'LightOpenID' => __DIR__ . '/../vendor/fp/lightopenid/openid.php'
));

$mapClassLoader->register();

```

### Step 3: Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Fp\OpenIdBundle\FpOpenIdBundle(),
    );
}
```

### Step 4: Configure your application's security.yml

In order for Symfony's security component to use the FpOpenIdBundle, you must
tell it to do so in the `security.yml` file. The `security.yml` file is where the
basic configuration for the security of your application is contained.

Below is a minimal example of the configuration necessary to use the FpOpenIdBundle
in your application:

``` yaml
# app/config/security.yml
security:
    providers:
        in_memory:
            memory:
                users:
                    user:  { password: userpass, roles: [ 'ROLE_USER' ] }

    firewalls:
        main:
            pattern: ^/

            fp_openid: ~

            logout:       true
            anonymous:    true

    access_control:
        - { path: ^/login_openid$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/secured_area, role: IS_AUTHENTICATED_OPENID }
```

Take a look at and examine the `firewalls` section. Here we have declared a
firewall named `main`. By specifying `fp_opeind`, you have told the Symfony2
framework that any time a request is made to this firewall that leads to the
user needing to authenticate himself, the user will be redirected to a form
where he will be able to enter identity provider url.

The `access_control` section is where you specify the credentials necessary for
users trying to access specific parts of your application. The bundle requires
that the login form be available to unauthenticated users but use the same firewall as
the pages you want to secure with the bundle. This is why you have specified that
the any request matching the `/login_openid` pattern have been made available to anonymous users.
You have also specified that any request beginning with `/secured_area` will require
a user to have the `IS_AUTHENTICATED_OPENID` role.

For more information on configuring the `security.yml` file please read the Symfony2
security component [documentation](http://symfony.com/doc/current/book/security.html).

### Step 5: Import FpOpenIdBundle routing file

Now that you have activated and configured the bundle, all that is left to do is
import the FpOpenIdBundle routing file.

By importing the routing file you will have made ready to use the login page.

In YAML:

``` yaml
# app/config/routing.yml
fp_openid_security:
    resource: "@FpOpenIdBundle/Resources/config/routing/security.xml"

```

Or if you prefer XML:

``` xml
<!-- app/config/routing.xml -->
<import resource="@FpOpenIdBundle/Resources/config/routing/security.xml"/>
```

### Step 6: Try It!

Go to the `/login_openid` page. You have to see a form which asks you to enter identity provider url. Let's use Google as authentication provider.

```
https://www.google.com/accounts/o8/id
```

Congratulations! You have been authenticated by Google. Now, you can try access `/secured_area`.

### Next Steps

Now that you have completed the basic installation and configuration of the
FpOpenIdBundle, you are ready to learn about more advanced features and usages
of the bundle.

The following documents are available:

- [Configuration Reference](configuration_reference.md)