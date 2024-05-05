<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ExternalOAuthService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class SocialiteController extends Controller
{
    private const array SOCIALITE_PROVIDERS = ['osm'];
    private string $provider = 'osm';
    private SocialiteUser $socialiteUser;


    public function redirectToProvider($provider): SymfonyRedirectResponse|RedirectResponse
    {
        if (!$this->checkSocialiteProvider($provider)) {
            return redirect()->route('login');
        }

        if ($provider === 'osm') {
            return $this->osmRedirect();
        }
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider): RedirectResponse
    {
        if (!$this->checkSocialiteProvider($provider)) {
            return redirect()->route('login');
        }
        $this->provider = $provider;

        $this->socialiteUser = Socialite::driver($provider)->user();

        if (Auth::user()) {
            return $this->authFlow();
        }
        return $this->nonAuthFlow();
    }

    private function authFlow(): RedirectResponse
    {
        if (Auth::user()->externalOAuthService()->where('provider', $this->provider)->exists()) {
            Auth::user()->externalOAuthService()->update([
                'token' => $this->socialiteUser->token,
                'refresh_token' => $this->socialiteUser->refreshToken ?? null,
            ]);
        } else {
            Auth::user()->externalOAuthService()->create([
                'provider' => $this->provider,
                'provider_id' => $this->socialiteUser->getId(),
                'token' => $this->socialiteUser->token,
                'refresh_token' => $this->socialiteUser->refreshToken ?? null,
            ]);
        }

        return redirect()->route('profile.edit');
    }

    private function nonAuthFlow(): RedirectResponse
    {
        $socialite = ExternalOAuthService::where('provider', $this->provider)
            ->where('provider_id', $this->socialiteUser->getId())
            ->first();
        $user = $socialite->user ?? null;

        if (!$user) {
            $user = $this->createSocialiteUser();
        }

        Auth::login($user);
        return redirect()->route('dashboard');
    }


    private function checkSocialiteProvider($provider): bool
    {
        return in_array($provider, self::SOCIALITE_PROVIDERS);
    }

    private function osmRedirect()
    {
        return Socialite::driver('osm')
            ->setScopes(config('services.osm.scope', ['read_prefs', 'write_api']))
            ->redirect();
    }

    private function createSocialiteUser(): User
    {
        //ToDo: check for users with the same name
        $user = User::create([
            'name' => $this->socialiteUser->getName(),
            'email' => $this->socialiteUser->getEmail() ?? '',
        ]);

        $user->socialite()->create([
            'provider' => $this->provider,
            'provider_id' => $this->socialiteUser->getId(),
            'token' => $this->socialiteUser->token,
            'refresh_token' => $this->socialiteUser->refreshToken ?? null,
        ]);

        return $user;
    }
}
