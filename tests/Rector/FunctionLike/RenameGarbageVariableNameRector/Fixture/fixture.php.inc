<?php

/** @noinspection ALL */
collect($array)->filter(static function ($value, int $key): bool {
    return 2 === $key;
});

/**
 * @param  mixed  $value
 */
function filter($value, int $key): bool
{
    return 2 === $key;
}

function array_is_list(array $array): bool
{
    $nextKey = -1;

    foreach ($array as $key => $value) {
        if ($key !== ++$nextKey) {
            return false;
        }
    }

    return true;
}

?>
-----
<?php

/** @noinspection ALL */
collect($array)->filter(static function ($_, int $key): bool {
    return 2 === $key;
});

/**
 * @param mixed $_
 */
function filter($_, int $key): bool
{
    return 2 === $key;
}

function array_is_list(array $array): bool
{
    $nextKey = -1;

    foreach ($array as $key => $_) {
        if ($key !== ++$nextKey) {
            return false;
        }
    }

    return true;
}

?>
