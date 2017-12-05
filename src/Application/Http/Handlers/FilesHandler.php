<?php

namespace DropParty\Application\Http\Handlers;

use DropParty\Domain\Files\FileRepository;
use DropParty\Domain\Users\AuthenticatedUser;
use DropParty\Infrastructure\ApplicationMonitor\ApplicationMonitor;
use DropParty\Infrastructure\Http\Handlers\AbstractViewHandler;
use DropParty\Infrastructure\View\Twig;
use Slim\Http\Request;
use Slim\Http\Response;

class FilesHandler extends AbstractViewHandler
{
    /**
     * @var AuthenticatedUser
     */
    private $authenticatedUser;

    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @param Twig $twig
     * @param ApplicationMonitor $applicationMonitor
     * @param AuthenticatedUser $authenticatedUser
     * @param FileRepository $fileRepository
     */
    public function __construct(Twig $twig, ApplicationMonitor $applicationMonitor, AuthenticatedUser $authenticatedUser, FileRepository $fileRepository)
    {
        parent::__construct($twig, $applicationMonitor);
        $this->authenticatedUser = $authenticatedUser;
        $this->fileRepository = $fileRepository;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return 'files.html.twig';
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $files = $this->fileRepository->findByUserId($this->authenticatedUser->getUserId());

        return $this->render($request, $response, ['files' => $files]);
    }
}
