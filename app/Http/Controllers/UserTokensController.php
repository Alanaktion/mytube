<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserTokensController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        /** @var \App\Models\User */
        $user = $request->user();
        $token = $user->createToken($request->input('name'));
        return redirect('/user/account')->with('message', 'Created access token: ' . $token->plainTextToken);
    }

    public function destroy(Request $request, string $tokenId)
    {
        $request->user()->tokens()->where('id', $tokenId)->delete();
        return redirect('/user/account')->with('message', 'Access token revoked.');
    }
}
