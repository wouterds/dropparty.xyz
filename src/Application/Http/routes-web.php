<?php

use DropParty\Application\Http\Handlers\AccountHandler;
use DropParty\Application\Http\Handlers\Files\DirectHandler as FilesDirectHandler;
use DropParty\Application\Http\Handlers\Files\DownloadHandler as FilesDownloadHandler;
use DropParty\Application\Http\Handlers\Files\HashedIdHandler as FilesHashedIdHandler;
use DropParty\Application\Http\Handlers\Files\ViewHandler as FilesViewHandler;
use DropParty\Application\Http\Handlers\FilesHandler;
use DropParty\Application\Http\Handlers\HomeHandler;
use DropParty\Application\Http\Handlers\Integrations\DropboxHandler as IntegrationsDropboxHandler;
use DropParty\Application\Http\Handlers\Integrations\DropboxUnlinkHandler as IntegrationsDropboxUnlinkHandler;
use DropParty\Application\Http\Handlers\SignInHandler;
use DropParty\Application\Http\Handlers\SignInPostHandler;
use DropParty\Application\Http\Handlers\SignOutHandler;
use DropParty\Application\Http\Handlers\SignUpHandler;
use DropParty\Application\Http\Handlers\SignUpPostHandler;
use DropParty\Application\Http\Middlewares\Files\HashedIdMiddleware as FilesHashedIdMiddleware;
use DropParty\Application\Http\Middlewares\Users\AuthenticatedUserMiddleware as UsersAuthenticatedUserMiddleware;

$app->group(null, function () use ($app) {
    $app->get('/', HomeHandler::class)->setName('home');

    $app->get('/sign-up', SignUpHandler::class)->setName('sign-up');
    $app->post('/sign-up', SignUpPostHandler::class);
    $app->get('/sign-in', SignInHandler::class)->setName('sign-in');
    $app->post('/sign-in', SignInPostHandler::class);
    $app->get('/sign-out', SignOutHandler::class)->setName('sign-out');

    $app->get('/account', AccountHandler::class)->setName('account');

    $app->group('/integrations', function () use ($app) {
        $app->get('/dropbox', IntegrationsDropboxHandler::class)->setName('integrations.dropbox');
        $app->get('/dropbox/unlink', IntegrationsDropboxUnlinkHandler::class)->setName('integrations.dropbox.unlink');
    });

    $app->get('/files', FilesHandler::class)->setName('files');
    $app->get('/view/{id}', FilesViewHandler::class)->setName('filesView');
    $app->get('/direct/{id}', FilesDirectHandler::class)->setName('filesDirect');
    $app->get('/download/{id}', FilesDownloadHandler::class)->setName('filesDownload');
    $app->get('/{hashedId}', FilesHashedIdHandler::class)->add(FilesHashedIdMiddleware::class);
})->add(UsersAuthenticatedUserMiddleware::class);
