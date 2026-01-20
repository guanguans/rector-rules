<?php

/** @noinspection ALL */
collect($array)->filter(
    /**
     * @param  mixed  $value
     */
    static function ($value, int $key): bool {
        return 2 === $key;
    }
);

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
collect($array)->filter(
    /**
     * @param  mixed  $value
     */
    static function ($_, int $key): bool {
        return 2 === $key;
    }
);

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
