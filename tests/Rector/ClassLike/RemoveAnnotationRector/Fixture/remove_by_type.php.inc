<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\ClassLike\RemoveAnnotationRector\Fixture;

use JMS\DiExtraBundle\Annotation as DI;

final class RemoveByType
{
    /**
     * @DI\InjectParams({
     *     "subscribeService" = 'some',
     *     "ipService" = 'some'
     * })
     */
    public function __construct()
    {
    }
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\ClassLike\RemoveAnnotationRector\Fixture;

use JMS\DiExtraBundle\Annotation as DI;

final class RemoveByType
{
    public function __construct()
    {
    }
}

?>
