<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap/app.php',
        __DIR__ . '/config',
        __DIR__ . '/database/seeders',
        __DIR__ . '/database/factories',
        __DIR__ . '/public/index.php',
        __DIR__ . '/resources/lang',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
        __DIR__ . '/ecs.php',
        __DIR__ . '/rector.php',
    ])
    // No arguments: picks up the PHP version from composer.json, so this
    // stays correct automatically on future PHP bumps instead of needing
    // to be kept in sync by hand.
    ->withPhpSets();
