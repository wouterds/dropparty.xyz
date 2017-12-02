<?php

namespace DropParty\Application\ApiClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;

class DropPartyClient
{
    /**
     * @var GuzzleClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $baseUri;

    /**
     * @param string $baseUri
     */
    public function __construct(string $baseUri)
    {
        $this->baseUri = $baseUri;

        $this->client = new GuzzleClient([
            // Don't verify secured connections in debug mode
            RequestOptions::VERIFY => filter_var(getenv('DEBUG'), FILTER_VALIDATE_BOOLEAN) ? false : true,
        ]);
    }

    /**
     * @param string $resource
     * @param array $data
     * @return Response
     */
    public function get(string $resource, array $data = []): Response
    {
        $endpoint = $this->baseUri . $resource;
        $payload = [
            RequestOptions::QUERY => $data,
        ];

        return $this->client->request('GET', $endpoint, $payload);
    }

    /**
     * @param string $resource
     * @param array $data
     * @return Response
     */
    public function post(string $resource, array $data = []): Response
    {
        $endpoint = $this->baseUri . $resource;
        $payload = [
            RequestOptions::FORM_PARAMS => $data,
        ];

        return $this->client->request('POST', $endpoint, $payload);
    }
}
