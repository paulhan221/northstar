# Contribution Guidelines

We follow [PSR-2 Coding Style](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).
Be sure to configure [EditorConfig](http://editorconfig.org) to ensure you have proper indentation settings.

- Include namespace on the same line as opening `<?php`.
- Method names must be `camelCase`, but helper functions should be `snake_case`.
- Use array literals, e.g. `[]`, rather than old `array()` syntax.


### Code Sample
```php
<?php namespace Vendor\Package;

use FooInterface;
use BarClass as Bar;
use OtherVendor\OtherPackage\BazClass;

class Foo extends Bar implements FooInterface
{
    /**
     * Sample function.
     *
     * @param String a - A parameter.
     * @param String b - Another parameter.
     * @return void
     */
    public function sampleFunction($a, $b = null)
    {
        if ($a === $b) {
            bar();
        } elseif ($a > $b) {
            $foo->bar($arg1);
        } else {
            BazClass::bar($arg2, $arg3);
        }
    }

    /**
     * Another sample function.
     */
    final public static function bar()
    {
        // method body
    }
}

```
