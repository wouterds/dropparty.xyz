<?php

namespace DropParty\Application\Filesystem;

use DropParty\Domain\Dropbox\Token;
use Emgag\Flysystem\Hash\HashPlugin;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxFilesystem extends Filesystem
{
    /**
     * @param Token $token
     * @return DropboxFilesystem
     */
    public static function filesystemForDropboxToken(Token $token): DropboxFilesystem
    {
        $dropboxClient = new DropboxClient(
            $token->getAccessToken(),
            null,
            1024 * 1024 * getenv('DROPBOX_MAX_CHUNK_SIZE')
        );
        $dropboxAdapter = new DropboxAdapter($dropboxClient);

        $filesystem = new DropboxFilesystem($dropboxAdapter);
        $filesystem->addPlugin(new HashPlugin());

        return $filesystem;
    }
}
