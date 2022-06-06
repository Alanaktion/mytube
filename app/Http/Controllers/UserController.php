<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function account(Request $request)
    {
        /** @var \App\Models\User */
        $user = $request->user();
        return view('user.account', [
            'user' => $user,
            'tokens' => $user->tokens()->get(),
        ]);
    }

    public function updateAccount(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);
        $user->update($request->all());
        return redirect('/user/account')->with('message', 'Account information updated.');
    }
}
