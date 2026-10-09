<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\ClassLike\RemoveAnnotationRector\Fixture;

/**
 * @method getName()
 */
final class Fixture
{
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\ClassLike\RemoveAnnotationRector\Fixture;

final class Fixture
{
}

?>
