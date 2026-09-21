<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\LdapService;
use Illuminate\Support\Facades\Hash;
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
                    'username' => $ldapUser['username'],
                    'email' => $ldapUser['email'], // Might be null
                    'password' => Hash::random(64), // Random password, never used
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

        return null; // Fortify will handle the failure response
    }
}

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $username = (string) $request->username;

            return Limit::perMinute(5)->by($username.$request->ip());
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::authenticateUsing(function (Request $request) {
            $credentials = $request->only('username', 'password');

            // Try to authenticate with LDAP first if enabled
            if (config('ldap.enabled', false)) {
                $ldapUser = $this->attemptLdapAuth(
                    $credentials['username'],
                    $credentials['password']
                );

                if ($ldapUser) {
                    // Find or create local user from LDAP
                    $user = User::where('ldap_id', $ldapUser['id'])->first();

                    if (!$user) {
                        // Create new user from LDAP data
                        $user = User::create([
                            'name' => $ldapUser['name'] ?? $credentials['username'],
                            'username' => $credentials['username'],
                            'ldap_id' => $ldapUser['id'],
                            'password' => bcrypt(\Str::random(40)), // Random password since LDAP handles auth
                        ]);
                    } else {
                        // Update user info from LDAP
                        $user->update([
                            'name' => $ldapUser['name'] ?? $user->name,
                        ]);
                    }

                    return $user;
                }
            }

            // Fallback to database authentication
            if (Auth::attempt($credentials)) {
                return Auth::user();
            }

            return null;
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

    }

    /**
     * Attempt LDAP authentication.
     *
     * @param  string  $username
     * @param  string  $password
     * @return array|null
     */
    protected function attemptLdapAuth($username, $password)
    {
        try {
            $ldapHost = config('ldap.host');
            $ldapPort = config('ldap.port', 389);
            $ldapBaseDn = config('ldap.base_dn');
            $ldapUsername = config('ldap.username');
            $ldapPassword = config('ldap.password');

            if (!$ldapHost || !$ldapBaseDn) {
                return null;
            }

            $ldapConnection = ldap_connect($ldapHost, $ldapPort);

            if ($ldapConnection) {
                ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($ldapConnection, LDAP_OPT_REFERRALS, 0);

                // Bind with admin credentials to search for user
                if (ldap_bind($ldapConnection, $ldapUsername, $ldapPassword)) {
                    // Search for user by username
                    $filter = sprintf('(|(uid=%s)(sAMAccountName=%s)(cn=%s))',
                        ldap_escape($username, '', LDAP_ESCAPE_FILTER),
                        ldap_escape($username, '', LDAP_ESCAPE_FILTER),
                        ldap_escape($username, '', LDAP_ESCAPE_FILTER)
                    );

                    $result = ldap_search($ldapConnection, $ldapBaseDn, $filter);
                    $entries = ldap_get_entries($ldapConnection, $result);

                    if ($entries['count'] > 0) {
                        $userEntry = $entries[0];
                        $userDn = $userEntry['dn'];

                        // Try to bind with user's DN and provided password
                        if (@ldap_bind($ldapConnection, $userDn, $password)) {
                            // Authentication successful
                            return [
                                'id' => $userEntry['objectguid'][0] ?? $userEntry['entryuuid'][0] ?? md5($userDn),
                                'name' => $userEntry['cn'][0] ?? $userEntry['name'][0] ?? $username,
                                'dn' => $userDn,
                                'username' => $username,
                            ];
                        }
                    }
                }

                ldap_unbind($ldapConnection);
            }
        } catch (\Exception $e) {
            // Log error but don't expose details
            \Log::error('LDAP authentication failed: ' . $e->getMessage());
        }

        return null;
    }
}
