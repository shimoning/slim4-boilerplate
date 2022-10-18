<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', \App\Application\Actions\Top\ShowAction::class)->setName('top');

    $app->group('/users', function (Group $group) {
        $group->get('', \App\Application\Actions\User\ListUsersAction::class);
        $group->get('/{id}', \App\Application\Actions\User\ViewUserAction::class);
    });
};
