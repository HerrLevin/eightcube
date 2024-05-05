<?php

namespace App\Providers;

use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class OsmProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ['read', 'write'];

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://www.openstreetmap.org/oauth2/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://www.openstreetmap.org/oauth2/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return parent::getTokenFields($code) + ['grant_type' => 'authorization_code'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://api.openstreetmap.org/api/0.6/user/details.json',
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer '.$token,
                ],
            ]
        );

        $json = json_decode($response->getBody(), true);

        return $json['user'];
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => $user['display_name'],
            'name'     => $user['display_name'],
            'email'    => null,
            'avatar'   => Arr::get($user, 'img.href', null),
        ]);
    }
}
