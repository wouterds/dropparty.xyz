<?php

namespace DropParty\Application\Http\Handlers;

use DropParty\Application\ApiClient\DropPartyClient;
use DropParty\Infrastructure\ApplicationMonitor\ApplicationMonitor;
use DropParty\Infrastructure\Http\Handlers\AbstractViewHandler;
use DropParty\Infrastructure\View\Twig;
use Exception;
use Slim\Http\Request;
use Slim\Http\Response;

class FilesHandler extends AbstractViewHandler
{
    /**
     * @var DropPartyClient
     */
    private $dropPartyClient;

    /**
     * @param Twig $twig
     * @param ApplicationMonitor $applicationMonitor
     * @param DropPartyClient $dropPartyClient
     */
    public function __construct(Twig $twig, ApplicationMonitor $applicationMonitor, DropPartyClient $dropPartyClient)
    {
        parent::__construct($twig, $applicationMonitor);

        $this->dropPartyClient = $dropPartyClient;
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
        try {
            $apiResponse = $this->dropPartyClient->get('/files.list', [
                'uid' => $request->getCookieParam('uid'),
            ]);
        } catch (Exception $e) {
            return $this->render($request, $response, ['apiResponse' => 'failed']);
        }

        $apiResponse = json_decode((string) $apiResponse->getBody(), true);

        return $this->render($request, $response, ['apiResponse' => $apiResponse]);
    }
}
