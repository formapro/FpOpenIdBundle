Configure User Manager
======================

You now have a working firewall. Users are redirected to a login page, then they authenticate themselves on an OpenID provider.
Upon success, the OpenID provider will send you back an identity token. It is your responsibility to link this (OpenID) identity to a valid `User` Symfony2 object, so we will configure an User Manager.
It is also a good idea to store these identities in the database.

_**Note** : Here we describe how to create an user "in the code". In [this section](interactive_user_creation.md), we describe an "interactive" method._

### Step 1. Create Identity class

#### General instructions

The bundle provides base classes which are already mapped for most fields to make it easier to create your entity.
Here is how you use it:

1. Extend the base `Identity` class (the exact class to use depends of your storage).
2. Implement `UserIdentityInterface` interface
3. Map the `id` field. It must be protected as it is inherited from the parent class.
4. Create `user` field and map it.

**Warning:**

> When you extend the mapped superclass provided by the bundle, don't redefine the mapping for the other fields.

In the following sections, you'll see examples of how your `Identity` class should look, depending on how you're storing your identities.

Your `Identity` class can live inside any bundle in your application.

**Warning:**

> If you override the `__construct()` method in your Identity class, be sure to call `parent::__construct()`, as the base Identity class depends on this to initialize some fields.

#### 1-a. Example: Doctrine ORM Identity class

If you're persisting your users via the Doctrine ORM, then your `Identity` class should live in the `Entity` namespace of your bundle and look like this (using annotations):

```php
<?php
// src/Acme/DemoBundle/Entity/OpenIdIdentity.php

namespace Acme\DemoBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\Mapping as ORM;

use Fp\OpenIdBundle\Entity\UserIdentity as BaseUserIdentity;
use Fp\OpenIdBundle\Model\UserIdentityInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="openid_identities")
 */
class OpenIdIdentity extends BaseUserIdentity
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
      * The relation is made eager by purpose. 
      * More info here: {@link https://github.com/formapro/FpOpenIdBundle/issues/54}
      * 
      * @var Symfony\Component\Security\Core\User\UserInterface
      *
      * @ORM\ManyToOne(targetEntity="Acme\DemoBundle\Entity\User", fetch="EAGER")
      * @ORM\JoinColumns({
      *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
      * })
      */
    protected $user;
    
    /*
     * It inherits an "identity" string field,
     * and an "attributes" text field
     */

    public function __construct()
    {
        parent::__construct();
        // your own logic (nothing for this example)
    }
}
```

If you are using YAML, it should look like something like this:
```yaml
# Acme\DemoBundle\Resources\config\doctrine\OpenIdIdentity.yml
Acme\DemoBundle\Entity\OpenIdIdentity:
    type: entity
    repositoryClass: Acme\DemoBundle\Entity\OpenIdIdentityRepository
    fields:
        id:
            type: integer
            id: true
            generator:
                strategy: AUTO
    manyToOne:
        user:
            targetEntity: User
            fetch: EAGER
            joinColumn:
                name: user_id
                referencedColumnName: id
    lifecycleCallbacks: { }
```

#### 1-b. Example: Doctrine MongoDB ODM Identity class

If you're persisting your users via the Doctrine MongoDB ODM, then your `User` class should live in the `Document` namespace of your bundle and look like this:

```php
<?php
// src/Acme/DemoBundle/Document/OpenIdIdentity.php

namespace Acme\DemoBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

use Fp\OpenIdBundle\Document\UserIdentity as BaseUserIdentity;

/**
 * @MongoDB\Document(collection="openid_identities")
 */
class OpenIdIdentity extends BaseUserIdentity
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;
    
    /**
     * {@inheritdoc}
     * @MongoDB\String
     */
    protected $identity;

    /**
     * {@inheritdoc}
     * @MongoDB\Hash
     */
    protected $attributes;

    /**
     * @var Symfony\Component\Security\Core\User\UserInterface
     *
     * @MongoDB\ReferenceOne(targetDocument="Acme\DemoBundle\Document\User", simple=true)
     */
    protected $user;

    public function __construct() 
    {
        parent::__construct();
        // your own logic
    }
}
```

### Step 2. Configure FpOpenIdBundle

Now when we have our own Identity class we can tell the bundle about it:

#### 2-a. With Doctrine ORM

```yaml
# app/config/config.yml
fp_open_id:
    db_driver: orm
    identity_class: Acme\DemoBundle\Entity\OpenIdIdentity
```
#### 2-b. With Doctrine MongoDB ODM

```yaml
# app/config/config.yml
fp_open_id:
    db_driver: mongodb
    identity_class: Acme\DemoBundle\Document\OpenIdIdentity
```

### Step 3. Create User Manager

#### 3-a. Create Manager

After a successful authentication with the OpenID provider, FpOpenIdBundle `UserManager` will be called - precisely the `loadUserByIdentity` method. It searches for the corresponding OpenIdIdentity in the database. If not found, your User Manager `createUserFromIdentity` method will be called.

Let's implement our own logic.
To create your `UserManager` class you have to implement `UserManagerInterface` or extend `UserManager` and overwrite `createUserFromIdentity` method.

Let's go the second way:

##### 3-a-a. For Doctrine ORM (comprehensive example)

_**Note**: we assume you store users in your database with an `User` entity, which has an `e-mail` address property._

```php
<?php
//src/Acme/DemoBundle/Security/User/OpenIdUserManager.php

namespace Acme\DemoBundle\Security\User;

use Fp\OpenIdBundle\Model\UserManager;
use Fp\OpenIdBundle\Model\IdentityManagerInterface;
use Doctrine\ORM\EntityManager;
use Acme\DemoBundle\Entity\OpenIdIdentity;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

class OpenIdUserManager extends UserManager
{
    /**
     * @param string $identity
     *  an OpenID token. With Google it looks like:
     *  https://www.google.com/accounts/o8/id?id=SOME_RANDOM_USER_ID
     * @param array $attributes
     *  requested attributes (explained later). At the moment just
     *  assume there's a 'contact/email' key
     */
    public function createUserFromIdentity($identity, array $attributes = array())
    {
        // put your user creation logic here
        // what follows is a typical example
        
        if (false === isset($attributes['contact/email'])) {
            throw new \Exception('We need your e-mail address!');
        }
        // in this example, we fetch User entities by e-mail
        $user = $this->entityManager->getRepository('AcmeDemoBundle:User')->findOneBy(array(
            'e-mail' => $attributes['contact/email']
        ));

        if (null === $user) {
            throw new BadCredentialsException('No corresponding user!');
        }

        // we create an OpenIdIdentity for this User
        $openIdIdentity = new OpenIdIdentity();
        $openIdIdentity->setIdentity($identity);
        $openIdIdentity->setAttributes($attributes);
        $openIdIdentity->setUser($user);

        $this->entityManager->persist($openIdIdentity);
        $this->entityManager->flush();
        
        // end of example

        return $user; // you must return an UserInterface instance (or throw an exception)
    }
    
    // we used an EntityManager, so inject it in constructor
    public function __construct(IdentityManagerInterface $identityManager, EntityManager $entityManager)
    {
        parent::__construct($identityManager);

        $this->entityManager = $entityManager;
    }
}
```

##### 3-a-b. For Doctrine MongoDB ODM

```php
<?php
//src/Acme/DemoBundle/Security/User/OpenIdUserManager.php

namespace Acme\DemoBundle\Security\User;

use Fp\OpenIdBundle\Model\UserManager;

class OpenIdUserManager extends UserManager
{
    public function createUserFromIdentity($identity, array $attributes = array())
    {
        // put your user creation logic here (for a comprehensive example check above)

        return $user; // you must return an UserInterface instance or throw an exception.
    }
}
```

#### 3-b. Set as a service

##### 3-b-a. For Doctrine ORM

```yaml
# src/Acme/DemoBundle/Resources/config/services.yml
services:
    acme.demo.openid_user_manager:
        class: Acme\DemoBundle\Entity\OpenIdUserManager
        # we used an EntityManager, so don't forget it in dependency injection
        # you may want to adapt it, only the IdentityManager is mandatory
        arguments: [@fp_openid.identity_manager, @doctrine.orm.entity_manager]
        
```

##### 3-b-b. For Doctrine MongoDB ODM

```yaml
# src/Acme/DemoBundle/Resources/config/services.yml
services:
    acme.demo.openid_user_manager:
        class: Acme\DemoBundle\Document\OpenIdUserManager
        arguments: [@fp_openid.identity_manager]
```

### Step 4. Configure OpenId Firewall

Now we can update the `security.yml` file with some new settings.
The main point is to add our new User Manager as a provider.

```yaml
# app/config/security.yml
security:
    firewalls:
        main:
            pattern:    ^/
            logout:     true
            anonymous:  true
            fp_openid:
                create_user_if_not_exists: true # so createUserFromIdentity method will be called
                provider: openid_user_manager # cf below
                # previously we used 'contact/email' field. So we have to request it!
                # Please note that all OpenID providers may not be able to provide all fields.
                # check the desired provider documentation
                required_attributes:
                    - contact/email
    providers:
        # the order is important here
        openid_user_manager:
            id: acme.demo.openid_user_manager # the name of the service
        # keep your database provider below! it may look like this:
        database:
            entity: { class: AcmeDemoBundle:User, property: username }
    # end of changes
    access_control:
        - { path: ^/login_openid$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/secured_area, role: IS_AUTHENTICATED_OPENID }
```

That's it! Your OpenID login should now be working.
You can use the `/logout` url to logout users.

**Important note:**

> Don't forget to keep in sync `OpenIdIdentities` and `User` entities!
> For example, when an `User` is removed, remove the corresponding `OpenIdIdentity` (no more used).
> Within our previous example, if an `User` e-mail address is modified, we should remove the `OpenIdIdentity` too. Otherwise he will still be able to log in with it!

Return to [index](index.md) to learn more