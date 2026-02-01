<?php

/** @noinspection ALL */
// @formatter:off
// phpcs:ignoreFile
enum Suit: string
{
    case Heart = 'Heart';
    case Diamond = 'Diamond';
    case Club = 'Club';
}
Suit::Heart;

?>
-----
<?php

/** @noinspection ALL */
// @formatter:off
// phpcs:ignoreFile
enum Suit: string
{
    case Heart = 'Heart';
    case Diamond = 'Diamond';
    case Club = 'Club';
}
Suit::HEART;

?>
