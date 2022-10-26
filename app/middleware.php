<?php

declare(strict_types=1);

use Slim\App;
use App\Application\Middleware\SessionMiddleware;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\Csrf\Guard;

return function (App $app) {
    $app->add(SessionMiddleware::class);
    $app->add(TwigMiddleware::createFromContainer($app, Twig::class));
    $app->add(Guard::class);
};
