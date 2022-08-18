@extends('layouts.app')

@section('content')
<div class="container max-w-6xl lg:py-10">
    <div class="mt-6 sm:mt-0">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-slate-900 dark:text-neutral-100">Personal Information</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-neutral-400">
                        This information is used to identify you and send you important information.
                    </p>
                </div>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <form action="{{ route('users.update', auth()->id()) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <x-card>
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <label for="name" class="form-label mb-2">
                                    {{ __('Name') }}
                                </label>
                                <x-input type="text" name="name" id="name" autocomplete="name" value="{{ $user->name }}" required />
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="email" class="form-label mb-2">
                                    {{ __('Email') }}
                                </label>
                                <x-input type="email" name="email" id="email" autocomplete="email" value="{{ $user->email }}" required />
                            </div>
                        </div>
                        <x-slot name="footer">
                            <x-button type="submit" primary>
                                {{ __('Save') }}
                            </x-button>
                        </x-slot>
                    </x-card>
                </form>
            </div>
        </div>

        <div class="hidden sm:block" aria-hidden="true">
            <div class="py-5">
                <div class="border-t dark:border-neutral-700"></div>
            </div>
        </div>

        <div class="md:grid md:grid-cols-3 md:gap-6 mt-8 sm:mt-0">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-slate-900 dark:text-neutral-100">Access Tokens</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-neutral-400">
                        These tokens are used to access your account from other applications.
                    </p>
                </div>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <x-card>
                    @if ($tokens->count() > 0)
                        <div class="-my-2 lg:-my-4">
                            @foreach($tokens as $token)
                                <div class="flex items-center py-2 lg:py-4 border-b dark:border-neutral-700 last:border-0">
                                    <div class="mr-auto">
                                        <div class="font-medium">{{ $token->name }}</div>
                                        <div class="text-sm text-slate-600 dark:text-neutral-400">Created {{ $token->created_at->format('Y-m-d') }}</div>
                                    </div>
                                    @if ($token->last_used_at)
                                        <div>Last used {{ $token->last_used_at->format('Y-m-d') }}</div>
                                    @else
                                        <div>Never used</div>
                                    @endif
                                    <form class="ml-4" action="{{ route('users.tokens.destroy', [auth()->id(), $token->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit">Revoke</x-button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                        <x-slot name="footer">
                            <form class="flex mt-1" action="{{ route('users.tokens.store', auth()->id()) }}" method="POST">
                                @csrf
                                <label for="token-name" class="sr-only">Token name</label>
                                <x-input class="max-w-md mr-3" name="name" id="token-name" placeholder="Token name" required />
                                <x-button class="flex-shrink-0 ml-auto" type="submit" primary>Create token</x-button>
                            </form>
                        </x-slot>
                    @else
                        <div class="py-8 lg:py-12 flex flex-col items-center">
                            <div class="mb-4 flex flex-col items-center">
                                <div class="font-medium">No tokens</div>
                                <div class="text-slate-600 dark:text-neutral-400">You have not created any access tokens yet.</div>
                            </div>
                            <form class="flex gap-3 mt-1" action="{{ route('users.tokens.store', auth()->id()) }}" method="POST">
                                @csrf
                                <label for="token-name" class="sr-only">Token name</label>
                                <x-input name="name" id="token-name" placeholder="Token name" required />
                                <x-button class="flex-shrink-0" type="submit" primary>Create token</x-button>
                            </form>
                        </div>
                    @endif
                </x-card>
            </div>
        </div>
    </div>
</div>
@endsection
