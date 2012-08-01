FpOpenIdBundle Configuration Reference
=====================================

All available configuration options are listed below with their default values.

``` yaml
# app/config/config.yml
fp_openid:
    db_driver:              null
    identity_class:         null

    template:
        engine: twig
```

This is an example of `security.yml` with all possible options:

``` yaml
# app/config/security.yml
# app/config/security.yml
security:
    providers:
        fp_openidbundle:
            id: fp_openid.user_manager #

    firewalls:
        main:
            pattern: ^/
            logout:       true
            anonymous:    true

            fp_openid:
                # options added or changed by openid listener:
                login_path:                   /login_openid
                check_path:                   /login_check_openid
                create_user_if_not_exists:    false
                relying_party:                fp_openid.relying_party.default
                required_attributes:          []
                optional_attributes:          []

                # options come with abstract listener
                check_path:                   /login_check
                use_forward:                  false
                always_use_default_target_path: false
                default_target_path:          /
                target_path_parameter:        _target_path
                use_referer:                  false
                failure_path:                 null
                failure_forward:              false
                remember_me:                  true
                provider:                     ~
                success_handler:              ~
                failure_handler:              ~

    access_control:
        - { path: ^/secured_area, role: IS_AUTHENTICATED_OPENID }
```