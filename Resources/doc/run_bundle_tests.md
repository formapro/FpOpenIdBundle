Run the bundle tests
====================

* Download the bundle (for example by cloning git):

```bash
$ git clone git://github.com/formapro/FpOpenIdBundle.git FpOpenIdBundle
$ cd FpOpenIdBundle
```

* Install vendors:

```bash
curl -s http://getcomposer.org/installer | php
php composer.phar install
```

* Run tests:

```bash
./bin/phpunit
```

Enjoy!
