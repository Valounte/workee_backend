<?php

return (new PhpCsFixer\Config)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
    ]);