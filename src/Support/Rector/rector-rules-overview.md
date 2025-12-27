# 1 Rules Overview

## NewExceptionToNewAnonymousExtendsExceptionImplementsRector

New exception to new anonymous extends exception implements

:wrench: **configure it!**

- class: [`Guanguans\RectorRules\Support\Rector\NewExceptionToNewAnonymousExtendsExceptionImplementsRector`](NewExceptionToNewAnonymousExtendsExceptionImplementsRector.php)

```diff
-new \Exception('Testing');
+new class('Testing') extends \Exception implements \Guanguans\MonorepoBuilderWorker\Contracts\ThrowableContract
+{
+};
```

<br>
