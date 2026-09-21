public function store(UserRequest $request)
{
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'is_ldap_user' => false,
    ]);

    $this->syncPermissions($request, $user);
    
    // Assign application role (independent of LDAP)
    if ($request->has('role')) {
        $user->assignRole($request->role);
    }

    return redirect()
        ->route("user.index")
        ->with('success', __('item created successfully'));
}

public function update(UserRequest $request, User $user)
{
    $user->name = $request->name;
    $user->email = $request->email;
    if ($request->password != '')
        $user->password = Hash::make($request->password);

    $user->save();
    $this->syncPermissions($request, $user);
    
    // Update application role
    if ($request->has('role')) {
        $user->syncRoles([$request->role]);
    }

    return redirect()
        ->route("user.index")
        ->with('warning', __('item updated successfully'));
}