<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $username = (string) $request->username; // Changed from email to username
            return Limit::perMinute(5)->by($username.$request->ip());
        });

        // Custom authentication using LDAP
        Fortify::authenticateUsing(function (Request $request) {
            $username = $request->username;
            
            // Try LDAP authentication first
            $ldapUser = \App\Ldap\User::findBy('uid', $username) 
                ?? \App\Ldap\User::findBy('sAMAccountName', $username)
                ?? \App\Ldap\User::findBy('mail', $username);
            
            if ($ldapUser && $ldapUser->auth()->attempt($request->password)) {
                // Find or create local user record
                $user = User::where('ldap_id', $ldapUser->getConvertedGuid())
                    ->first();
                
                if (!$user) {
                    $user = User::create([
                        'name' => $ldapUser->getName(),
                        'email' => $ldapUser->getEmail() ?? $username . '@company.local',
                        'ldap_id' => $ldapUser->getConvertedGuid(),
                        'is_ldap_user' => true,
                        'password' => Hash::make(random_bytes(32)), // Random password
                    ]);
                } else {
                    // Update user info from LDAP
                    $user->update([
                        'name' => $ldapUser->getName(),
                        'email' => $ldapUser->getEmail() ?? $user->email,
                    ]);
                }
                
                return $user;
            }
            
            // Fallback to database authentication for non-LDAP users
            $user = User::where('email', $username)
                ->orWhere('name', $username)
                ->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

            return null;
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}