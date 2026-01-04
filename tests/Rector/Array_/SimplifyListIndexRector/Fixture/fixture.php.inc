<?php

/** @noinspection ALL */
[0 => 'foo', 1 => 'bar', 2 => 'baz'];
[0 => 'foo', 'bar', 2 => 'baz'];

?>
-----
<?php

/** @noinspection ALL */
['foo', 'bar', 'baz'];
['foo', 'bar', 'baz'];

?>
