<?php

/** @noinspection ALL */
// @formatter:off
// phpcs:ignoreFile
enum Enum: string
{
    case CaseName = 'CaseName';
}
Enum::CaseName;

?>
-----
<?php

/** @noinspection ALL */
// @formatter:off
// phpcs:ignoreFile
enum Enum: string
{
    case CASE_NAME = 'CaseName';
}
Enum::CASE_NAME;

?>
