<?php

use DropParty\Application\Http\Handlers\Api\Dropbox\Tokens\AddHandler as ApiDropboxTokensAddHandler;
use DropParty\Application\Http\Handlers\Api\Files\AddAccessLogHandler as ApiFilesAddAccessLogHandler;
use DropParty\Application\Http\Handlers\Api\Files\AddHandler as ApiFilesAddHandler;
use DropParty\Application\Http\Handlers\Api\Files\DownloadHandler as ApiFilesDownloadHandler;
use DropParty\Application\Http\Handlers\Api\Files\GetHandler as ApiFilesGetHandler;
use DropParty\Application\Http\Handlers\Api\Files\ListHandler as ApiFilesListHandler;
use DropParty\Application\Http\Handlers\Api\NotFoundHandler as ApiNotFoundHandler;
use DropParty\Application\Http\Handlers\Api\Users\AuthenticateHandler as ApiUsersAuthenticateHandler;
use DropParty\Application\Http\Handlers\Api\Users\RegisterHandler as ApiUsersRegisterHandler;
use DropParty\Application\Http\Middlewares\FilesAccessLogsMiddleware;

$app->get('/', ApiNotFoundHandler::class);

$app->group('/users', function () use ($app) {
    $app->post('.register', ApiUsersRegisterHandler::class)->setName('api.users.register');
    $app->post('.authenticate', ApiUsersAuthenticateHandler::class)->setName('api.users.authenticate');
});

$app->group('/files', function () use ($app) {
    $app->get('.list', ApiFilesListHandler::class)->setName('api.files.list');
    $app->get('.get', ApiFilesGetHandler::class)->setName('api.files.get');
    $app->get('.download', ApiFilesDownloadHandler::class)->setName('api.files.download')->add(FilesAccessLogsMiddleware::class);
    $app->post('.add', ApiFilesAddHandler::class)->setName('api.files.add');
    $app->post('.access-log.add', ApiFilesAddAccessLogHandler::class)->setName('api.files.add');
});

$app->group('/dropbox/tokens', function () use ($app) {
    $app->post('.add', ApiDropboxTokensAddHandler::class)->setName('api.dropbox.tokens.add');
});
