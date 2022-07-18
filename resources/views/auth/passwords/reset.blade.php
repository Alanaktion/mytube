@extends('layouts.app')

@section('content')
<div class="max-w-sm w-full mx-auto">
    <h2 class="mt-6 text-center text-3xl leading-9 font-bold text-slate-900 dark:text-neutral-300">
        {{ __('Reset Password') }}
    </h2>
    <form class="mt-8" action="{{ route('password.update') }}" method="POST">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <label for="email" class="block font-semibold text-slate-700 dark:text-neutral-300 mb-1">
            {{ __('E-Mail Address') }}
        </label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required class="dark:bg-neutral-800 dark:bg-opacity-75 border border-slate-300 dark:border-neutral-700 focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:focus:border-primary-500 rounded py-2 px-4 block w-full placeholder-slate-500 dark:placeholder-neutral-400 text-slate-900 dark:text-neutral-100 appearance-none leading-normal shadow-sm" autocomplete="email" autofocus>

        @error('email')
        <div class="my-2 text-red-600 dark:text-red-400" role="alert">
            <strong>{{ $message }}</strong>
        </div>
        @enderror


        <label for="password" class="block font-semibold text-slate-700 dark:text-neutral-300 mb-1">
            {{ __('Password') }}
        </label>
        <input id="password" name="password" type="password" required class="dark:bg-neutral-800 dark:bg-opacity-75 border border-slate-300 dark:border-neutral-700 focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:focus:border-primary-500 rounded py-2 px-4 block w-full placeholder-slate-500 dark:placeholder-neutral-400 text-slate-900 dark:text-neutral-100 appearance-none leading-normal shadow-sm" autocomplete="new-password">

        @error('password')
        <div class="my-2 text-red-600 dark:text-red-400" role="alert">
            <strong>{{ $message }}</strong>
        </div>
        @enderror


        <div class="mt-6">
            <x-button type="submit" rounded primary>
                {{ __('Reset Password') }}
            </x-button>
        </div>
    </form>
</div>
@endsection
