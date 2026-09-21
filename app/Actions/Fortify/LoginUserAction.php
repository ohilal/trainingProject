<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\LdapService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginUserResponse;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\LoginViewResponse;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Laravel\Fortify\Events\Login;
use Illuminate\Contracts\Auth\StatefulGuard;

class LoginUserAction
{
    protected $guard;
    protected $ldapService;

    public function __construct(StatefulGuard $guard, LdapService $ldapService)
    {
        $this->guard = $guard;
        $this->ldapService = $ldapService;
    }

    public function handle(LoginRequest $request): LoginUserResponse
    {
        $credentials = $request->only(Fortify::username(), 'password');
        $username = $credentials[Fortify::username()];
        $password = $credentials['password'];

        // 1. Attempt LDAP Authentication
        $ldapUser = $this->ldapService->authenticate($username, $password);

        if ($ldapUser) {
            // 2. Find or Create Local User
            $user = User::where('username', $username)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $ldapUser['full_name'] ?? $username,
                    'username' => $ldapUser['username'],
                    'email' => $ldapUser['email'], // Might be null
                    'password' => Hash::make(Str::random(64)), // Random password, never used
                    'ldap_id' => $ldapUser['ldap_id'],
                    'guid' => $ldapUser['guid'],
                    'is_ldap_user' => true,
                ]);
                
                // OPTIONAL: Assign default role here if needed
                // $user->assignRole('student'); 
            } else {
                // Update info if changed in LDAP
                $user->update([
                    'email' => $ldapUser['email'],
                    'ldap_id' => $ldapUser['ldap_id'],
                ]);
            }

            // 3. Log the user in
            $this->guard->login($user, $request->boolean('remember'));
            
            event(new Login($user));

            return app(LoginUserResponse::class);
        }

        // 4. If LDAP fails, deny access (No fallback to DB password for LDAP users)
        // You can optionally allow local admin login here if desired
        $localUser = User::where('username', $username)->where('is_ldap_user', false)->first();
        
        if ($localUser && Hash::check($password, $localUser->password)) {
             $this->guard->login($localUser, $request->boolean('remember'));
             event(new Login($localUser));
             return app(LoginUserResponse::class);
        }

        throw ValidationException::withMessages([
            Fortify::username() => __('auth.failed'),
        ]);
    }
}