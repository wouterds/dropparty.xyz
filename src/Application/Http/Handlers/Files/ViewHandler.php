<?php

namespace DropParty\Application\Http\Handlers\Files;

use DropParty\Domain\Files\FileId;
use DropParty\Domain\Files\FileRepository;
use DropParty\Infrastructure\ApplicationMonitor\ApplicationMonitor;
use DropParty\Infrastructure\Http\Handlers\AbstractViewHandler;
use DropParty\Infrastructure\View\Twig;
use Slim\Http\Request;
use Slim\Http\Response;

class ViewHandler extends AbstractViewHandler
{
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @param Twig $twig
     * @param ApplicationMonitor $applicationMonitor
     * @param FileRepository $fileRepository
     */
    public function __construct(Twig $twig, ApplicationMonitor $applicationMonitor, FileRepository $fileRepository)
    {
        parent::__construct($twig, $applicationMonitor);
        $this->fileRepository = $fileRepository;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return 'files/view.html.twig';
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function __invoke(Request $request, Response $response, string $id): Response
    {
        $fileId = new FileId($id);
        $file = $this->fileRepository->find($fileId);

        if (empty($file)) {
            return $response->withStatus(400);
        }

        return $this->render($request, $response, ['file' => $file]);
    }
}
