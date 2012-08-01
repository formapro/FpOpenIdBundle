Getting Started With FpOpenIdBundle
===================================

## Installation

### Step 1. Download FpOpenIdBundle

Ultimately, the required files should be downloaded to the `vendor` directory.

This can be done in several ways, depending on your preference. The first method described here is the standard method for Symfony 2.0.
If you prefer use git submodule [read this tutorial](install_as_git_submodules.md) and come back to the next step.

**Usingthe vendors script**

Add the following lines in your `deps` file:

```
[FpOpenIdBundle]
    git=git://github.com/formapro/FpOpenIdBundle.git
    target=bundles/Fp/OpenIdBundle
    version=origin/1.2

[LightOpenId]
    git=git://github.com/formapro/LightOpenID.git
```

Now, run the vendors script to download the bundle and the library:

```bash
$ php bin/vendors install
```

### Step 2: Configure the Autoloader

Add `Fp` namespace to your autoloader:

```php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Fp' => __DIR__.'/../vendor/bundles',
));

```

Add auto loading rule for `LightOpenId`:
```php
<?php
// app/autoload.php

require_once __DIR__.'/../vendor/symfony/src/Symfony/Component/ClassLoader/MapClassLoader.php';

$mapClassLoader = new \Symfony\Component\ClassLoader\MapClassLoader(array(
    'LightOpenID' => __DIR__ . '/../vendor/LightOpenId/openid.php'
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
    factories:
        - %kernel.root_dir%/../vendor/bundles/Fp/OpenIdBundle/Resources/config/security_factories.xml

    providers:
        in_memory:
            users:
                admin: { password: kitten, roles: 'ROLE_ADMIN' }

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

- [Configure User Manager\Provider](configure_user_manager.md)
- [Interactive User Creation](interactive_user_creation.md)
- [Configuration Reference](configuration_reference.md)