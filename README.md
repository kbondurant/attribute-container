# Attribute Container

An attribute based container for [league/container](https://container.thephpleague.com/).

## Installation

Via Composer

```bash
composer require kbondurant/attribute-container
```

## Requirements

The following versions of PHP are supported by this version.

* PHP 8.0
* PHP 8.1

## Usage

This container allows you to bind an interface to its implementation using [PHP Attributes](https://www.php.net/manual/en/language.attributes.php)

### Add AttributeContainer as a delegate container to your league/container
```php
<?php 

declare(strict_types=1);

$container = new League\Container\Container();
$delegate  = new Kbondurant\AttributeContainer\AttributeContainer();

$container->delegate($delegate);
```

### Regular binding
```php
<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Kbondurant\AttributeContainer\BindTo;

#[BindTo(Bar::class)]
interface Foo
{
    //
}
```

### Singleton binding
```php
<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Kbondurant\AttributeContainer\BindTo;

#[BindTo(Bar::class, true)]
interface Foo
{
    //
}
```

### Custom binding attribute

If you do not want to rely on the provided BindTo attribute you can create your own attribute class that implements BindingAttribute interface

```php
#[Attribute]
class MyBindingAttribute implements Kbondurant\AttributeContainer\BindingAttribute
{
    /**
     * @param class-string $class
     */
    public function __construct(
        private string $class,
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function isShared(): bool
    {
        return false;
    }
}

#[MyBindingAttribute(Bar::class)]
interface Foo
{
    //
}
```

## Testing

Testing includes PHPUnit and PHPStan (Level 9).
``` bash
$ composer test
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
