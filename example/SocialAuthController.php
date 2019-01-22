<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use App\User;
use Auth;

/**
 * Class SocialAuthController
 * @package App\Http\Controllers\Auth
 */
class SocialAuthController extends Controller
{
    use AuthenticatesUsers;
    /**
     * redirect to external authorisation service url
     *
     * @param $service
     * @return mixed
     */
    public function redirect($service)
    {
        return Socialite::driver($service)->redirect();
    }

    /**
     * validate oauth token and login
     *
     * @param Request $request
     * @param string $service
     * @throws
     * @return null
     */
    public function callback(Request $request, $service)
    {
        try {
            // validate oauth code and get remote user
            $remoteUser = Socialite::driver($service)->stateless()->user();
            // find or create local user
            $localUser = $this->findOrCreateUser($remoteUser, $service);
            // login local user
            Auth::login($localUser);
            // return to login response, we are using laravel function for session regenerate (SECURITY!) and redirect to homepage
            return $this->sendLoginResponse($request);
        }
        catch (\Exception $ex)
        {
            /*
             * Some custom logging?
             */
            return $this->sendFailedLoginResponse($request);
        }
    }

    /**
     * find or create new user in local user database
     *
     * @param $remoteUser
     * @param $provider
     * @return mixed
     */
    private function findOrCreateUser($remoteUser, $provider)
    {
        // search first only with provider name and user id combination
        $authUser = User::where('provider', $provider)->where('provider_id', $remoteUser->id )->first();
        if ($authUser) {
            $authUser->name = $remoteUser->name;
            $authUser->save();
            // found user, return user
            return $authUser;
        }
        // search on email address without provider information
        $authUser = User::where('email', $remoteUser->email)->whereNull('provider')->whereNull('provider_id')->first();
        if ($authUser) {
            $authUser->name = $remoteUser->name;
            $authUser->provider = $provider;
            $authUser->provider_id = $remoteUser->id;
            $authUser->save();
            // found user with email address, update user and return saved user
            return $authUser;
        }
        // Create new user and return new user
        return User::create([
            'name'     => $remoteUser->name,
            'email'    => $remoteUser->email,
            'provider' => $provider,
            'provider_id' => $remoteUser->id,
            'password' => uniqid()
        ]);
    }

}