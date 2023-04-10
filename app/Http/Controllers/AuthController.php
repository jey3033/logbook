<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller {
    public function redirectToProvider() {
        return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallback(Request $request)
        {
            try {
                $user_google    = Socialite::driver('google')->user();
                $user           = User::where('email', $user_google->getEmail())->first();
    
                //jika user ada maka langsung di redirect ke halaman home
                //jika user tidak ada maka simpan ke database
                //$user_google menyimpan data google account seperti email, foto, dsb
    
                if($user != null){
                    \auth()->login($user, true);
                    return redirect()->route('home');
                } else {
                    $create = User::Create([
                        'email'             => $user_google->getEmail(),
                        'name'              => $user_google->getName(),
                        'password'          => 0,
                        'email_verified_at' => now()
                    ]);
            
                    
                    \auth()->login($create, true);
                    return redirect()->route('home');
                }
            } catch (\Exception $e) {
                return redirect()->route('login');
            }
        }
}

?>