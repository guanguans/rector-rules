# rector-rules

> [!NOTE]
> A set of rector/rector rules. - 一套 rector/rector 规则。

[![tests](https://github.com/guanguans/rector-rules/actions/workflows/tests.yml/badge.svg)](https://github.com/guanguans/rector-rules/actions/workflows/tests.yml)
[![php-cs-fixer](https://github.com/guanguans/rector-rules/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/guanguans/rector-rules/actions/workflows/php-cs-fixer.yml)
[![codecov](https://codecov.io/gh/guanguans/rector-rules/graph/badge.svg?token=0RtgSGom4K)](https://codecov.io/gh/guanguans/rector-rules)
[![Latest Stable Version](https://poser.pugx.org/guanguans/rector-rules/v)](https://packagist.org/packages/guanguans/rector-rules)
[![GitHub release (with filter)](https://img.shields.io/github/v/release/guanguans/rector-rules)](https://github.com/guanguans/rector-rules/releases)
[![Total Downloads](https://poser.pugx.org/guanguans/rector-rules/downloads)](https://packagist.org/packages/guanguans/rector-rules)
[![License](https://poser.pugx.org/guanguans/rector-rules/license)](https://packagist.org/packages/guanguans/rector-rules)

## Requirement

* PHP >= 7.4

## Installation

```shell
composer require guanguans/rector-rules --dev --ansi -v
```

## Usage

### :monocle_face: [Rules Overview](docs/rules-overview.md)

### In your rector configuration register rules

```php
use Guanguans\RectorRules\Rector\Array_\SimplifyListIndexRector;
use Guanguans\RectorRules\Rector\Declare_\AddNoinspectionsDocCommentToDeclareRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withConfiguredRule(AddNoinspectionsDocCommentToDeclareRector::class, [
        '*/tests/*' => [
            'AnonymousFunctionStaticInspection',
            'NullPointerExceptionInspection',
            'PhpUnhandledExceptionInspection',
            'StaticClosureCanBeUsedInspection',
        ],
    ])
    // ...
    ->withRules([
        SimplifyListIndexRector::class,
        // ...
    ]);
```

## Composer scripts

```shell
composer checks:required
composer php-cs-fixer:fix
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

* [guanguans](https://github.com/guanguans)
* [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
