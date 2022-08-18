<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserTokensController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(User $user, Request $request)
    {
        $this->authorize('update', $user);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $token = $user->createToken($request->input('name'));
        return redirect()->route('users.show', $user)
            ->with('message', 'Created access token: ' . $token->plainTextToken);
    }

    public function destroy(User $user, string $tokenId)
    {
        $this->authorize('update', $user);
        $user->tokens()->where('id', $tokenId)->delete();
        return redirect()->route('users.show', $user)
            ->with('message', 'Access token revoked.');
    }
}
