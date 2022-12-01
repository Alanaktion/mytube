<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TokensController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $this->authorize('update', $user);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $token = $user->createToken($request->input('name'));
        return redirect()->route('users.show', $user)
            ->with('message', 'Created access token: ' . $token->plainTextToken);
    }

    public function destroy(string $tokenId, Request $request)
    {
        $user = $request->user();
        $this->authorize('update', $user);
        $user->tokens()->where('id', $tokenId)->delete();
        return redirect()->route('users.show', $user)
            ->with('message', 'Access token revoked.');
    }
}
