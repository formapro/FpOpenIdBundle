FpOpenIdBundle
==============

The FpOpenIdBundle adds support of [openid authentication](http://openid.net/) to symfony's security. It uses [LightOpenId](http://gitorious.org/lightopenid) as a relying party(client).

Features include:

- Fully integrated to Symfony's Security
- Unit tested

**Note:** Previous versions of the bundle [1.0](https://github.com/formapro/FpOpenIdBundle/tree/1.0) for Symfony 2.0.x and [1.1](https://github.com/formapro/FpOpenIdBundle/tree/1.1) for Symfony 2.1.x
are not supported any more.

**Caution:** This bundle is developed in sync with [symfony's repository](https://github.com/symfony/symfony).

[![Build Status](https://secure.travis-ci.org/formapro/FpOpenIdBundle.png?branch=master)](http://travis-ci.org/formapro/FpOpenIdBundle)

Documentation
-------------

The bulk of the documentation is stored in the `Resources/doc/index.md` file in this bundle:

[Read the Documentation for master](FpOpenIdBundle/blob/master/Resources/doc/index.md)

[Read the Documentation for 1.2.0 (for Symfony 2.0.x)](https://github.com/formapro/FpOpenIdBundle/blob/1.2/Resources/doc/index.md)

Installation
------------

All the installation instructions are located in [documentation](https://github.com/formapro/FpOpenIdBundle/blob/master/Resources/doc/index.md).

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

About
-----

FpOpenIdBundle is a [formapro](https://github.com/formapro) initiative.
See also the list of [contributors](https://github.com/formapro/FpOpenIdBundle/contributors).

Credits
-------

* [nurikabe](https://github.com/nurikabe) for very early feedback.
* [klebba](https://github.com/klebba) for suggestions to new version.
* [Koc](https://github.com/Koc) for point me out to a very good UserProvider implementation.
* The first versions inspired by [OpenIDAuthBundle](https://github.com/KainHaart/OpenIDAuthBundle).
* `UserManager` inspired by [FOSOAuthServerBundle](https://github.com/FriendsOfSymfony/FOSOAuthServerBundle) bundle.
* Installation doc adapted from [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle) doc.
* [formapro](https://github.com/formapro) for supporting open source movement.
* [dr-fozzy](https://github.com/dr-fozzy) for adding support of mongodb driver.

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/formapro/FpOpenIdBundle/issues). 
Read this [note](https://github.com/formapro/FpOpenIdBundle/blob/master/Resources/doc/run_bundle_tests.md) to find out how to run the bundle's tests.