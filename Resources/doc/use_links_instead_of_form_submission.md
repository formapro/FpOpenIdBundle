Use Links Instead Of Form Submissions
=====================================

In case you need to authenticate with OpenID provider using links only (without form submission):

1. Disable ```post_only``` requirement in your firewall:

    ```yml
    # app/config/security.yml
    security:
        firewalls:
            acme:
                pattern:   ^/
                form_login:
                    post_only: false
                fp_openid: ~
                logout:    true
                anonymous: true
    ```

2. Alter the login template:

    ```twig
    {% extends "FpOpenIdBundle::layout.html.twig" %}

    {% block fp_openid_content %}
        {% if error %}
            <div>{{ error }}</div>
        {% endif %}
        
        <a href="{{ path("fp_openid_security_check", {'openid_identifier': 'http://www.google.com/o8/openid'}) }}">Google</a>
    {% endblock fp_openid_content %}
    ```

    Or, if you use [fixed list of OpenID providers](fixed_list_of_openid_providers.md):

    ```twig
    {% extends "FpOpenIdBundle::layout.html.twig" %}

    {% block fp_openid_content %}
        {% if error %}
            <div>{{ error }}</div>
        {% endif %}

        <ul>
            {% for provider in valid_openid_providers %}
            <li><a href="{{ path("fp_openid_security_check", {'openid_identifier': provider.url}) }}">{{ provider.name }}</a></li>
            {% endfor %}
        </ul>
    {% endblock fp_openid_content %}
    ```
    