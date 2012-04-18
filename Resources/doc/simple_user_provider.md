Simple User Provider
====================

If you want to have a user in the token you have to configure user provider. It could be done several ways.
Here will be described a way when a user can be created in the code.
It means that you can create a user instance without asking user about any information like email and so on.

### Configure Identity class

The bundle provides base classes which are already mapped for most fields
to make it easier to create your entity. Here is how you use it:

1. Extend the base `Identity` class (the class to use depends of your storage)
2. Map the `id` field. It must be protected as it is inherited from the parent class.

**Warning:**

> When you extend from the mapped superclass provided by the bundle, don't
> redefine the mapping for the other fields as it is provided by the bundle.

In the following sections, you'll see examples of how your `Identity` class should
look, depending on how you're storing your identities.

Your `Identity` class can live inside any bundle in your application.

**Warning:**

> If you override the __construct() method in your Identity class, be sure
> to call parent::__construct(), as the base Identity class depends on
> this to initialize some fields.

**a) Doctrine ORM Identity class**

If you're persisting your users via the Doctrine ORM, then your `User` class
should live in the `Entity` namespace of your bundle and look like this to
start:

```php
<?php
namespace Acme\DemoBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\Mapping as ORM;

use Fp\OpenIdBundle\Entity\Identity as BaseIdentity;
use Fp\OpenIdBundle\Model\UserIdentityInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="openid_identities")
 */
class OpenIdIdentity extends BaseIdentity implements UserIdentityInterface
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Symfony\Component\Security\Core\User\UserInterface
     *
     * @ORM\OneToOne(targetEntity="Acme\DemoBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * {@iheritdoc}
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * {@iheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }
}

```

### Configure FpOpenIdBundle

Now when we have our own Identity class we can tell the bundle about it:

``` yaml
# app/config/config.yml
fp_openid:
    db_driver: orm
    identity_class: Acme\DemoBundle\Entity\OpenIdIdentity
```

### Configure UserManager

**a) Create manager**

To create your `UserManager` class you have to implement `UserManagerInterface` or extend UserManager and overwrite `createUserFromIdentity` method.
Lets do the simpler second way.

```php
<?php
namespace Acme\DemoBundle\Entity;

use Fp\OpenIdBundle\Model\UserManager;

class OpenIdUserManager extends UserManager
{
    public function createUserFromIdentity($identity, array $attributes = array())
    {
        //put your user creation login here

        return $user // must always return UserInterface instance or throw an exception.
    }
}

```

**b) Add to container**

```yaml
# Acme\DemoBundle\Resources\config\services.yml

services:
    acme.demo.openid_user_manager:
        class: Acme\DemoBundle\Entity\OpenIdUserManager

```

### Configure OpenId Firewall

```yaml
app/config/security.yml

# app/config/security.yml
security:
    providers:
        openid_user_manager:
            id: acme.demo.openid_user_manager
    firewalls:
        main:
            fp_openid:
                create_user_if_not_exists: true
                provider: openid_user_manager

```

That's it!