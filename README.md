# rector-rules

> [!NOTE]
> A set of additional rules for rector/rector. - 一套针对 `rector/rector` 的附加规则。

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
use Guanguans\RectorRules\Rector\File\SortFileFunctionStmtRector;
use Guanguans\RectorRules\Rector\Name\RenameToPsrNameRector;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->registerDecoratingNodeVisitor(ParentConnectingVisitor::class)
    ->withConfiguredRule(RenameToPsrNameRector::class, [
        'assertMatches*Snapshot',
        'beforeEach',
        'PDO',
    ])
    // ...
    ->withRules([
        SortFileFunctionStmtRector::class,
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
