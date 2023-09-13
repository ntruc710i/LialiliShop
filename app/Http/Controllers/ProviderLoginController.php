<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class ProviderLoginController extends Controller
{
    /* Login With Provider Redirect */
    public function providerLoginRedirect($provider){
        return Socialite::driver($provider)->stateless()->redirect();
    }

    /* Login With Provider Callback */
    public function providerLoginCallback($provider){
        $providerUser = Socialite::driver($provider)->stateless()->user();

        $db_user = User::where([
            ['email', '=', $providerUser->email],
            ['provider_id', '!=', $providerUser->id],
        ])->first();

        if($db_user){
            return Redirect::to('http://localhost:8000/provider/login?message=email-duplicate');
        }

        $user = User::updateOrCreate([
            'provider_id' => $providerUser->id,
        ],
        [
            'name' => $providerUser->name,
            'email' => $providerUser->email,
            'provider_token' => $providerUser->token,
            'avatar' => $providerUser->avatar,
            'provider' => $provider,
            'password' => $providerUser->token,
            'role' => 'customer'
        ]);

        Session::put('email', $providerUser->email);

        return Redirect::to('http://localhost:8000/api/auth/provider/login/'.$providerUser->email);
    }

    /* Provider Login */
    public function providerLogin($email){
        if(Session::get('email', $email)){
            $user = User::where('email', $email)->first();

            $user["token"] = $user->createToken(time())->plainTextToken;

            Auth::login($user);

            return response()->json(['user' => $user]);
        }
    }
}
