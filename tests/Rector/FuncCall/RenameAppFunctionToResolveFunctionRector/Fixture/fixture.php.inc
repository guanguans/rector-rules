<?php

/** @noinspection ALL */
app();
app('request');
app(\Illuminate\Http\Request::class);
app(\Illuminate\Log\Logger::class, [
    'logger' => new \Psr\Log\NullLogger(),
]);

?>
-----
<?php

/** @noinspection ALL */
app();
resolve('request');
resolve(\Illuminate\Http\Request::class);
resolve(\Illuminate\Log\Logger::class, [
    'logger' => new \Psr\Log\NullLogger(),
]);

?>
