Sweet Tooth for OroCRM
========================

Welcome to Sweet Tooth customer loyalty for OroCRM.

This document contains information on how to download, install, and start
using Sweet Tooth.

Requirements
------------

Sweet Tooth requires Symfony 2.4, Doctrine 2 and PHP 5.4.4 or above.

Installation
------------

### Use as dependency in composer.json in your OroCRM root

```yaml
    "require": {
        "sweettooth/sweettooth-orocrm": "1.0.*",
    }
```

### Update dependencies
```bash
    composer update
```

### Run a database update to create Sweet Tooth tables

```bash
    php app/console doctrine:schema:update
```

### Run the cron to sync OroCRM and Sweet Tooth (should be setup to run periodically)

```bash
     php app/console oro:cron:sweettooth:sync
```