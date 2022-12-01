<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(User::class);
    }

    public function show(User $user)
    {
        return view('user.account', [
            'user' => $user,
            'tokens' => $user->tokens()->get(),
        ]);
    }

    public function update(User $user, Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);
        $user->update($request->all());
        return redirect()->route('users.show', $user)->with('message', 'Account information updated.');
    }
}
