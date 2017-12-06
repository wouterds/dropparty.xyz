<?php

namespace DropParty\Application\Filesystem;

use Emgag\Flysystem\Hash\HashPlugin;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Adapter\Local as LocalAdapter;

class LocalFilesystem extends Filesystem
{
    /**
     * @return LocalFilesystem
     */
    public static function filesystem(): LocalFilesystem
    {
        $localAdapter = new LocalAdapter(APP_DIR . getenv('FILESYSTEM_DIR'));

        $filesystem = new LocalFilesystem($localAdapter);
        $filesystem->addPlugin(new HashPlugin());

        return $filesystem;
    }
}
