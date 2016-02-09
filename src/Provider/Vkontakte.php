<?php

namespace League\OAuth2\Client\Provider;

use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\Exception\VkontakteProviderException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;

class Vkontakte extends AbstractProvider
{
    /**
     * Production API URL.
     *
     * @const string
     */
    const BASE_API_URL = 'https://api.vk.com';

    /**
     * Production Graph API URL.
     *
     * @const string
     */
    const BASE_OAUTH_URL = 'https://oauth.vk.com';

    /**
     * The Graph API version to use for requests.
     *
     * @var string
     */
    protected $apiVersion;

    /**
     * @param array $options
     * @param array $collaborators
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);

        if (empty($options['apiVersion'])) {
            $message = 'The "graphApiVersion" option not set. Please set a default Graph API version.';
            throw new \InvalidArgumentException($message);
        }

        $this->apiVersion = $options['apiVersion'];
    }

    public function getBaseAuthorizationUrl()
    {
        return $this::BASE_OAUTH_URL.'/authorize';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return $this::BASE_OAUTH_URL.'/access_token';
    }

    public function getDefaultScopes()
    {
        return ['email'];
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $fields = implode(',', [
            'nickname', 'screen_name', 'sex', 'bdate', 'city', 'country', 'timezone',
            'photo_50', 'photo_100', 'photo_200_orig',
            'has_mobile', 'contacts', 'education', 'online', 'counters', 'relation', 'last_seen', 'status',
            'can_write_private_message', 'can_see_all_posts', 'can_see_audio', 'can_post', 'universities',
            'schools', 'verified',
        ]);

        return $this::BASE_API_URL.'/method/users.get?user_id='.$token->getResourceOwnerId().'&fields='.$fields.'&access_token='.$token;
    }

    public function getAccessToken($grant = 'authorization_code', array $params = [])
    {
        if (isset($params['refresh_token'])) {
            throw new VkontakteProviderException('Vkontakte does not support token refreshing.');
        }

        return parent::getAccessToken($grant, $params);
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new VkontakteUser($response);
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            $message = $data['error']['type'].': '.$data['error']['message'];
            throw new IdentityProviderException($message, $data['error']['code'], $data);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getContentType(ResponseInterface $response)
    {
        $type = parent::getContentType($response);

        return $type;
    }

}
