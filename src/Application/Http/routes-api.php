<?php

use DropParty\Application\Http\Handlers\Api\Files\AddHandler as ApiFilesAddHandler;
use DropParty\Application\Http\Handlers\Api\NotFoundHandler as ApiNotFoundHandler;
use DropParty\Application\Http\Handlers\Api\Users\AuthenticateHandler as ApiUsersAuthenticateHandler;
use DropParty\Application\Http\Middlewares\Users\AuthenticatedUserMiddleware as UsersAuthenticatedUserMiddleware;

$app->group(null, function () use ($app) {
    $app->get('/', ApiNotFoundHandler::class);

    $app->group('/users', function () use ($app) {
        $app->post('.authenticate', ApiUsersAuthenticateHandler::class)->setName('api.users.authenticate');
    });

    $app->group('/files', function () use ($app) {
        $app->post('.add', ApiFilesAddHandler::class)->setName('api.files.add');
    });
})->add(UsersAuthenticatedUserMiddleware::class);
