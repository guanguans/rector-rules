<?php

/** @noinspection ALL */

/**
 * In the `\PhpParser\Node\FunctionLike` parameter,
 * the `\PhpParser\Node\Expr\Variable` node does not have a scope attribute.
 */
function func_name(string $var_name): string
{
    $new_var_name = $var_name;

    return $new_var_name;
}

?>
-----
<?php

/** @noinspection ALL */

/**
 * In the `\PhpParser\Node\FunctionLike` parameter,
 * the `\PhpParser\Node\Expr\Variable` node does not have a scope attribute.
 */
function func_name(string $varName): string
{
    $newVarName = $varName;

    return $newVarName;
}

?>
