@extends('layouts.app')

@section('content')
<div class="max-w-sm w-full mx-auto">
    <h2 class="mt-6 text-center text-3xl leading-9 font-bold text-slate-900 dark:text-neutral-300">
        {{ __('Login') }}
    </h2>
    <form class="mt-8" action="{{ route('login') }}" method="POST">
        @csrf
        <input type="hidden" name="remember" value="true">

        <div class="rounded-md shadow-xs">
            <div class="mb-3 lg:mb-4">
                <input aria-label="{{ __('Email') }}" name="email" type="email" required class="dark:bg-neutral-800/75 border border-slate-300 dark:border-neutral-700 focus:outline-hidden focus:ring-primary-500 focus:border-primary-500 dark:focus:border-primary-500 rounded-sm py-2 px-4 block w-full placeholder-slate-500 dark:placeholder-neutral-400 text-slate-900 dark:text-neutral-100 appearance-none leading-normal" placeholder="{{ __('Email') }}" value="{{ old('email') }}" autocomplete="email" autofocus>
            </div>
            <div>
                <input aria-label="{{ __('Password') }}" name="password" type="password" required class="dark:bg-neutral-800/75 border border-slate-300 dark:border-neutral-700 focus:outline-hidden focus:ring-primary-500 focus:border-primary-500 dark:focus:border-primary-500 rounded-sm py-2 px-4 block w-full placeholder-slate-500 dark:placeholder-neutral-400 text-slate-900 dark:text-neutral-100 appearance-none leading-normal" placeholder="{{ __('Password') }}" autocomplete="current-password">
            </div>
        </div>

        @error('email')
        <div class="my-2 text-red-600 dark:text-red-400" role="alert">
            <strong>{{ $message }}</strong>
        </div>
        @enderror

        <div class="mt-6 flex items-center justify-between">
            <x-button type="submit" rounded primary>
                {{ __('Login') }}
            </x-button>
            <div class="text-sm leading-5">
                <a href="{{ route('password.request') }}" class="font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 focus:outline-hidden focus:underline">
                    {{ __('Forgot your password?') }}
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
