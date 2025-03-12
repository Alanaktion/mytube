@extends('layouts.app')

@section('content')
<div class="max-w-sm w-full mx-auto">
    <h2 class="mt-6 text-center text-3xl leading-9 font-bold text-slate-900 dark:text-neutral-300">
        {{ __('Confirm Password') }}
    </h2>
    <form class="mt-8" action="{{ route('password.confirm') }}" method="POST">
        @csrf
        {{ __('Please confirm your password before continuing.') }}

        <label for="password" class="block font-semibold text-slate-700 dark:text-neutral-300 mb-1">
            {{ __('Password') }}
        </label>
        <input id="password" name="password" type="password" required class="dark:bg-neutral-800/75 border border-slate-300 dark:border-neutral-700 focus:outline-hidden focus:ring-primary-500 focus:border-primary-500 dark:focus:border-primary-500 rounded-sm py-2 px-4 block w-full placeholder-slate-500 dark:placeholder-neutral-400 text-slate-900 dark:text-neutral-100 appearance-none leading-normal shadow-xs" autocomplete="current-password" autofocus>

        @error('password')
        <div class="my-2 text-red-600 dark:text-red-400" role="alert">
            <strong>{{ $message }}</strong>
        </div>
        @enderror


        <div class="mt-6 flex items-center justify-between">
            <x-button type="submit" rounded primary>
                {{ __('Confirm Password') }}
            </x-button>

            @if (Route::has('password.request'))
            <div class="text-sm leading-5">
                <a href="{{ route('password.request') }}" class="font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 focus:outline-hidden focus:underline">
                    {{ __('Forgot Your Password?') }}
                </a>
            </div>
            @endif
        </div>
    </form>
</div>
@endsection
