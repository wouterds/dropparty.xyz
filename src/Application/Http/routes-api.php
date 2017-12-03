<?php

use DropParty\Application\Http\Handlers\Api\Files\AddHandler as ApiFilesAddHandler;
use DropParty\Application\Http\Handlers\Api\Files\ListHandler as ApiFilesListHandler;
use DropParty\Application\Http\Handlers\Api\NotFoundHandler as ApiNotFoundHandler;
use DropParty\Application\Http\Handlers\Api\Users\AuthenticateHandler as ApiUsersAuthenticateHandler;
use DropParty\Application\Http\Handlers\Api\Users\RegisterHandler as ApiUsersRegisterHandler;

$app->get('/', ApiNotFoundHandler::class);

$app->group('/users', function () use ($app) {
    $app->post('.register', ApiUsersRegisterHandler::class)->setName('api.users.register');
    $app->post('.authenticate', ApiUsersAuthenticateHandler::class)->setName('api.users.authenticate');
});

$app->group('/files', function () use ($app) {
    $app->get('.list', ApiFilesListHandler::class)->setName('api.files.list');
    $app->post('.add', ApiFilesAddHandler::class)->setName('api.files.add');
});
