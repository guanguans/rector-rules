<?php

namespace Guanguans\RectorRulesTests\Rector\Array_\SimplifyListIndexRector\Fixture;

[
    0 => 'delimiter',
    1 => 'orderbynull',
    2 => 'groupbyconst',
];

?>
-----
<?php

namespace Guanguans\RectorRulesTests\Rector\Array_\SimplifyListIndexRector\Fixture;

[
    'delimiter',
    'orderbynull',
    'groupbyconst',
];

?>
