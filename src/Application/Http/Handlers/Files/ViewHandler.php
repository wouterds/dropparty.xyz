<?php

namespace DropParty\Application\Http\Handlers\Files;

use DropParty\Application\ApiClient\DropPartyClient;
use DropParty\Infrastructure\ApplicationMonitor\ApplicationMonitor;
use DropParty\Infrastructure\Http\Handlers\AbstractViewHandler;
use DropParty\Infrastructure\View\Twig;
use Slim\Http\Request;
use Slim\Http\Response;

class ViewHandler extends AbstractViewHandler
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
        $apiResponse = $this->dropPartyClient->get('/files.get', ['id' => $id]);

        if ($apiResponse->getStatusCode() !== 200) {
            return $response->withStatus(400);
        }

        $contents = json_decode((string) $apiResponse->getBody(), true);

        if (empty($contents['data'])) {
            return $response->withStatus(400);
        }

        $file = $contents['data'];

        return $this->render($request, $response, ['file' => $file]);
    }
}
