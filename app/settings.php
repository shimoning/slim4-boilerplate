<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'app' => [
                    'APP_NAME'  => $_ENV['APP_NAME'],
                    'APP_URL'   => $_ENV['APP_URL'],
                    'APP_ENV'   => $_ENV['APP_ENV'],
                ],
                'db' => [
                    'DB_HOST'       => $_ENV['DB_HOST'],
                    'DB_PORT'       => $_ENV['DB_PORT'],
                    'DB_DATABASE'   => $_ENV['DB_DATABASE'],
                    'DB_USERNAME'   => $_ENV['DB_USERNAME'],
                    'DB_PASSWORD'   => $_ENV['DB_PASSWORD'],
                    'DB_CHARSET'    => $_ENV['DB_CHARSET'],
                    'DB_COLLATION'  => $_ENV['DB_COLLATION'],
                ],
                'path' => [
                    'database'  => __DIR__ . '/../database',
                    'public'    => __DIR__ . '/../public',
                    'src'       => __DIR__ . '/../src',
                    'log'       => __DIR__ . '/../logs',
                    'cache'     => __DIR__ . '/../var/cache',
                ],

                'displayErrorDetails' => !empty($_ENV['DISPLAY_ERROR']),
                'logError'            => !empty($_ENV['ERROR_LOGGING']),
                'logErrorDetails'     => !empty($_ENV['ERROR_LOGGING_DETAILS']),
                'logger' => [
                    'name' => 'slim-boilerplate',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'twig' => [
                    'debug' => $_ENV['TWIG_DEBUG'] ?? true,
                    'strict_variables' => $_ENV['TWIG_STRICT_VARIABLES'] ?? true,
                    'cache' => __DIR__ . '/../var/cache/twig',
                ],
            ]);
        }
    ]);
};
