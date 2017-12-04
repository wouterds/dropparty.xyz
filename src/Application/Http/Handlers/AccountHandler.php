<?php

namespace DropParty\Application\Http\Handlers;

use DropParty\Domain\Dropbox\TokenRepository;
use DropParty\Domain\Users\UserId;
use DropParty\Infrastructure\ApplicationMonitor\ApplicationMonitor;
use DropParty\Infrastructure\Http\Handlers\AbstractViewHandler;
use DropParty\Infrastructure\View\Twig;
use Slim\Http\Request;
use Slim\Http\Response;

class AccountHandler extends AbstractViewHandler
{
    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    /**
     * @param Twig $twig
     * @param ApplicationMonitor $applicationMonitor
     * @param TokenRepository $tokenRepository
     */
    public function __construct(Twig $twig, ApplicationMonitor $applicationMonitor, TokenRepository $tokenRepository)
    {
        parent::__construct($twig, $applicationMonitor);
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return 'account.html.twig';
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $data = [];

        $userId = new UserId($request->getCookieParam('uid'));
        $data['dropboxLinked'] = $this->tokenRepository->hasTokenForUserId($userId);

        return $this->render($request, $response, $data);
    }
}
