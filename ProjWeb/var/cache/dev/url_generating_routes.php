<?php

// This file has been auto-generated by the Symfony Routing Component.

return [
    'app_exemple_index' => [[], ['_controller' => 'App\\Controller\\ExempleController::index'], [], [['text', '/']], [], []],
    '_preview_error' => [['code', '_format'], ['_controller' => 'error_controller::preview', '_format' => 'html'], ['code' => '\\d+'], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '\\d+', 'code', true], ['text', '/_error']], [], []],
];
