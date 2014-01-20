Fixed List Of OpenID Providers
==================================

In case you ever need to use a certain list of valid OpenID providers, here's the solution.

1. Create such list in you parameters.yml file:

    ```yml

    parameters:
        valid_openid_providers:
        - { name: Steam, url: http://steamcommunity.com/openid }
        - { name: Google, url: https://www.google.com/accounts/o8/id }
    ```

2. Define a relying party service in services.yml:

    ```yml

    parameters:
        acme_openid.class: Acme\Bundle\Bridge\RestrictedOpenIdRelyingParty
    services:
        acme_openid:
            class: %acme_openid.class%
            arguments: ['@service_container']
    ```

3. Enable this relying party in your security config:

    ```yml
    security:
        firewalls:
            acme:
                pattern:   ^/
                fp_openid:
                    relying_party: acme_openid
                logout:    true
                anonymous: true
    ```

4. And finally an example of relying party code:

    ```php
    <?php
    // file: src/Acme/Bundle/Bridge/RelyingParty/RestrictedOpenIdRelyinParty.php
    namespace Acme\Bundle\Bridge\RelyingParty;

    use Symfony\Component\DependencyInjection\ContainerInterface as Container;

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\RedirectResponse;

    use Fp\OpenIdBundle\Bridge\RelyingParty\LightOpenIdRelyingParty;
    use Fp\OpenIdBundle\RelyingParty\IdentityProviderResponse;
    use Fp\OpenIdBundle\RelyingParty\Exception\OpenIdAuthenticationCanceledException;
    use Fp\OpenIdBundle\RelyingParty\Exception\OpenIdAuthenticationValidationFailedException;

    class RestrictedOpenIdRelyingParty extends LightOpenIdRelyingParty
    {
        private $container;

        public function __construct(Container $container) {
            $this->container = $container;
        }

        protected function guessIdentifier(Request $request)
        {
            foreach ($this->container->getParameter('valid_openid_providers') as $provider) {
                $providers[] = $provider['url'];
            }

            if(in_array($request->get('openid_identifier'), $providers)) {
                return $request->get('openid_identifier');
            } else {
                throw new OpenIdAuthenticationValidationFailedException("Invalid OpenID provider used", 1);
            }
        }
    }
    ```