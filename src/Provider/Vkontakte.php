<?php

namespace Mihalich1988\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Mihalich1988\OAuth2\Client\Provider\Exception\VkontakteProviderException;
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
     * @param array $options
     * @param array $collaborators
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
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
            'nickname', 'screen_name', 'sex', 'bdate', 'city', 'country', 'timezone', 'photo_max_orig',
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

    protected function prepareAccessTokenResponse(array $result)
    {
        $result['resource_owner_id'] = $result['user_id'];

        return $result;
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
