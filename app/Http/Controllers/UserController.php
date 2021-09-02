<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function account()
    {
        /** @var \App\Models\User */
        $user = Auth::user();
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

    public function createToken(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        /** @var \App\Models\User */
        $user = $request->user();
        $token = $user->createToken($request->input('name'));
        return redirect('/user/account')->with('message', 'Created access token: ' . $token->plainTextToken);
    }

    public function revokeToken(Request $request, string $tokenId)
    {
        $request->user()->tokens()->where('id', $tokenId)->delete();
        return redirect('/user/account')->with('message', 'Access token revoked.');
    }
}
