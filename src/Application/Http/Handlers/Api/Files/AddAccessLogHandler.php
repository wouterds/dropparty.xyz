<?php

namespace DropParty\Application\Http\Handlers\Api\Files;

use DropParty\Domain\Files\FileAccessLog;
use DropParty\Domain\Files\FileAccessLogRepository;
use DropParty\Domain\Files\FileId;
use DropParty\Domain\Files\FileRepository;
use Exception;
use Respect\Validation\Validator;
use Slim\Http\Request;
use Slim\Http\Response;

class AddAccessLogHandler
{
    /**
     * @var FileAccessLogRepository
     */
    private $fileAccessLogRepository;

    /**
     * @var FileAccessLogRepository|FileRepository
     */
    private $fileRepository;

    /**
     * @param FileRepository $fileRepository
     * @param FileAccessLogRepository $fileAccessLogRepository
     */
    public function __construct(FileRepository $fileRepository, FileAccessLogRepository $fileAccessLogRepository)
    {
        $this->fileAccessLogRepository = $fileAccessLogRepository;
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $this->validate($request);
        } catch (Exception $e) {
            return $response->withStatus(400);
        }

        $fileId = new FileId($request->getParam('file_id'));

        if (!$this->fileRepository->has($fileId)) {
            return $response->withStatus(400);
        }

        $fileAccessLog = new FileAccessLog(
            $fileId,
            $request->getParam('ip'),
            empty($request->getParam('user_agent')) ? null : $request->getParam('user_agent'),
            empty($request->getParam('referrer')) ? null : $request->getParam('referrer')
        );

        try {
            $this->fileAccessLogRepository->add($fileAccessLog);
        } catch (Exception $e) {
            return $response->withStatus(400);
        }

        return $response->withJson([
            'data' => $fileAccessLog,
        ]);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    private function validate(Request $request)
    {
        $validator = Validator::create();

        $validator->addRule(Validator::key('file_id', Validator::stringType()->notEmpty()));
        $validator->addRule(Validator::key('ip', Validator::stringType()->notEmpty()));
        $validator->addRule(Validator::key('user_agent', Validator::stringType()));
        $validator->addRule(Validator::key('referrer', Validator::stringType()));

        $validator->assert($request->getParams());
    }
}
