<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\Units\Coins\CoinsForRegisterNewUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        $username = trim((string) ($input['username'] ?? ''));

        if ($username === '') {
            $username = isset($input['email']) && $input['email'] !== ''
                ? explode('@', (string) $input['email'])[0]
                : preg_replace('/\s+/', '-', trim((string) ($input['name'] ?? '')));
        }

        $username = strtolower((string) preg_replace('/[^A-Za-z0-9._-]+/u', '-', $username));
        $username = trim($username, '-_.');

        if ($username === '') {
            $username = 'user';
        }

        $baseUsername = $username;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $input['username'] = $username;

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique(User::class),
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        $user =  User::create([
            'name' => $input['name'],
            'username' => $input['username'],
            'email' => $input['email'] ?? null,
            'password' => Hash::make($input['password']),
        ]);

       app(CoinsForRegisterNewUser::class)->executed($user);

        return $user;
    }
}
