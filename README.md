# WebmunkeezI18NBundle

This bundle unleashes a internationalization on Symfony applications.

## Installation

Use Composer to install this bundle:

```console
$ composer require webmunkeez/i18n-bundle
```

Add the bundle in your application kernel:

```php
// config/bundles.php

return [
    // ...
    Webmunkeez\I18NBundle\WebmunkeezI18NBundle::class => ['all' => true],
    // ...
];
```