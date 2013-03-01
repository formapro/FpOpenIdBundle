Interactive User Creation
=========================

In the section [Configure User Manager\Provider](configure_user_manager.md) it was described how to create user on the fly(in the code). Sometime it is not enough information to do so. Maybe you want for some extra information like the user email, first\last name and so. This section describe how you can create user interactively.

**Warning:**

> To proceed with this section you have to complete Steps 1,2 from [Configure User Manager\Provider](configure_user_manager.md).

###

### Step 1. Configure failure_path

To make it work we should define `failure_path` and `provider`.
```yaml
#app/config/security.yml

security:
    firewalls:
        secured_area:
            pattern:                                      ^/
            anonymous:                                    true
            fp_openid:                                    
                failure_path:                             /finish_login_openid
                provider:                                 openid_user_manager
                required_attributes:
                    -                                     contact/email
                    -                                     namePerson/first
                    -                                     namePerson/last

    providers:
        openid_user_manager:
            id:                                           fp_openid.user_manager

    access_control:
        - { path: ^/finish_login_openid$, role: IS_AUTHENTICATED_ANONYMOUSLY }
```

There are two main parameters: 

* *failure path: /finish_login_openid*. It means that for any failure happened while authentication a user will be redirected to this path. 
* *provider: openid_user_manager*. This option shows that we want use user manager to provider instance of `User`. Without this option openid provider will set to the token a string identity as user.

### Step 2. Create failure controller.

So now we can code the controller which will handle request to failure path: `/finish_login_openid`.

```php 
<?php
//src/Acme/DemoBundle/Controller/SecurityController.php
namespace Acme\DemoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Fp\OpenIdBundle\RelyingParty\RecoveredFailureRelyingParty;

class SecurityController extends Controller
{
    public function finishOpenIdLoginAction(Request $request)
    {
        //AuthenticationException or its child
        $failure = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        
        //you have to do:
        // 1) create user
        // 2) create identity
        // 3) store them to db

        //when you are done you can finish authentication process.
        return $this->redirect($this->generateUrl('fp_openid_security_check', array(
            RecoveredFailureRelyingParty::RECOVERED_QUERY_PARAMETER => 1
        )));
    }
```

Here is the controller example with [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle) integration:

```php 
<?php
//src/Acme/DemoBundle/Controller/SecurityController.php
namespace Acme\DemoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Acme\DemoBundle\Entity\User;

use Fp\OpenIdBundle\RelyingParty\Exception\OpenIdAuthenticationCanceledException;
use Fp\OpenIdBundle\RelyingParty\RecoveredFailureRelyingParty;
use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken

class SecurityController extends Controller
{
    public function finishOpenIdLoginAction(Request $request)
    {
        $failure = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        if (false == $failure) {
            throw new \LogicException('The controller expect AuthenticationException to be present in session');
        }
        if ($failure instanceof OpenIdAuthenticationCanceledException) {
            // do some action on cancel. Add a flash message etc.

            return $this->redirect('/');
        }

        /**
         * @var $token OpenIdToken
         */
        $token = $failure->getToken();
        if (false == $token instanceof OpenIdToken) {
            throw new \LogicException('The failure does not contain OpenIdToken, Is the failure come from openid?');
        }

        $attributes = array_merge(array(
            'contact/email' => '',
            'namePerson/first' => '',
            'namePerson/last' => '',
            ), $token->getAttributes())
        ;
        
        //the next code is pseudo. You have to adopt it to your needs.
        $user = $this->getUserManager()->createUser();
        $user->setUsername($attributes['contact/email']);
        $user->setFirstname($attributes['namePerson/first']);
        $user->setLastname($attributes['namePerson/last']);

        $form = $this->buildUserForm($user);
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $this->getUserManager()->updateUser($user);

                $identity = $this->getIdentityManager()->create();
                $identity->setIdentity($token->getIdentity());
                $identity->setAttributes($attributes);
                $identity->setUser($user);
                $this->getIdentityManager()->update($identity);

                return $this->redirect($this->generateUrl('fp_openid_security_check', array(
                    RecoveredFailureRelyingParty::RECOVERED_QUERY_PARAMETER => 1
                )));
            }
        }

        return $this->render('AcmeDemoBundle:Security:finishOpenIdLogin.html.twig', array(
            'form' => $form->createView()
        );
    }

    protected function buildUserForm(User $user)
    {
        return $this->createFormBuilder($user)
            ->add('username', 'email')
            ->add('firstname', 'text')
            ->add('lastname', 'text')
            ->getForm()
        ;
    }

    protected function getIdentityManager()
    {
        return $this->get('fp_openid.identity_manager');
    }

    protected function getUserManager()
    {
        return $this->get('fos_user.user_manager.default');
    }
```