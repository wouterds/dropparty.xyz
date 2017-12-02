<?php

namespace DropParty\Application\Oauth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class DropboxOauthProvider extends AbstractProvider
{
    /**
     * @return string
     */
    public function getBaseAuthorizationUrl(): string
    {
        return 'https://www.dropbox.com/oauth2/authorize';
    }

    /**
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return 'https://api.dropboxapi.com/oauth2/token';
    }

    /**
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return 'https://api.dropboxapi.com/2/users/get_current_account';
    }

    /**
     * @param AccessToken|string|null $token
     * @return array
     */
    protected function getAuthorizationHeaders($token = null): array
    {
        if ($token === null) {
            return [];
        }

        return ['Authorization' => 'Bearer ' . $token->getToken()];
    }

    /**
     * @return array
     */
    public function getDefaultScopes(): array
    {
        return [];
    }

    /**
     * @param ResponseInterface $response
     * @param array|string $data
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() !== 200) {
            throw new IdentityProviderException('Unexpected response', $response->getStatusCode(), $response->getBody());
        }
    }

    /**
     * @param array $response
     * @param AccessToken $token
     * @return GenericResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token): GenericResourceOwner
    {
        return new GenericResourceOwner($response, 'account_id');
    }

    /**
     * @param array $options
     * @return array
     */
    protected function getAuthorizationParameters(array $options): array
    {
        $parameters = parent::getAuthorizationParameters($options);

        if (isset($parameters['approval_prompt'])) {
            unset($parameters['approval_prompt']);
        }

        return $parameters;
    }

    /**
     * @param AccessToken $token
     * @return mixed
     */
    protected function fetchResourceOwnerDetails(AccessToken $token)
    {
        $url = $this->getResourceOwnerDetailsUrl($token);

        $request = $this->getAuthenticatedRequest(self::METHOD_POST, $url, $token);

        return $this->getParsedResponse($request);
    }
}
