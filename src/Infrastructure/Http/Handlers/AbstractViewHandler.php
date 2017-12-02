<?php

namespace DropParty\Infrastructure\Http\Handlers;

use DropParty\Infrastructure\ApplicationMonitor\ApplicationMonitor;
use DropParty\Infrastructure\View\Twig;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class AbstractViewHandler implements View
{
    /**
     * @var Twig
     */
    protected $twig;

    /**
     * @var ApplicationMonitor
     */
    private $applicationMonitor;

    /**
     * @param Twig $twig
     * @param ApplicationMonitor $applicationMonitor
     */
    public function __construct(Twig $twig, ApplicationMonitor $applicationMonitor)
    {
        $this->twig = $twig;
        $this->applicationMonitor = $applicationMonitor;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $data
     * @return Response
     */
    public function render(Request $request, Response $response, array $data = []): Response
    {
        // Extra info
        $data['app'] = [
            'request' => $request,
            'monitor' => $this->applicationMonitor,
            'env' => $_ENV,
        ];

        // Render template to response
        return $this->twig->renderWithResponse($response, $this->getTemplate(), $data);
    }
}
