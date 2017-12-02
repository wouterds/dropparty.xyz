<?php

namespace DropParty\Infrastructure\View;

use League\Container\ServiceProvider\AbstractServiceProvider;
use SPE\FilesizeExtensionBundle\Twig\FilesizeExtension;
use Twig_Loader_Filesystem;

class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        Twig::class,
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->container->share(Twig::class, function () {
            $loader = new Twig_Loader_Filesystem(APP_DIR . '/' . getenv('TEMPLATES_DIR'));
            $twig = new Twig($loader);
            $twig->addExtension(new FilesizeExtension());

            return $twig;
        });
    }
}
