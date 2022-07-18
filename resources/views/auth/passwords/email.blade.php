@extends('layouts.app')

@section('content')
<div class="max-w-sm w-full mx-auto">
    <h2 class="mt-6 text-center text-3xl leading-9 font-bold text-slate-900 dark:text-neutral-300">
        {{ __('Reset Password') }}
    </h2>
    <form class="mt-8" action="{{ route('password.email') }}" method="POST">
        @csrf
        @if (session('status'))
        <div class="flex items-center bg-emerald-100 dark:bg-emerald-900 bg-opacity-50 dark:bg-opacity-0 border border-emerald-100 dark:border-emerald-600 p-3 rounded mb-4">
            <svg class="w-6 h-6 mr-2 text-emerald-500 dark:text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-emerald-900 dark:text-emerald-100">
                {{ session('status') }}
            </span>
        </div>
        @endif

        <label for="email" class="block font-semibold text-slate-700 dark:text-neutral-300 mb-1">
            {{ __('E-Mail Address') }}
        </label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required class="dark:bg-neutral-800 dark:bg-opacity-75 border border-slate-300 dark:border-neutral-700 focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:focus:border-primary-500 rounded py-2 px-4 block w-full placeholder-slate-500 dark:placeholder-neutral-400 text-slate-900 dark:text-neutral-100 appearance-none leading-normal shadow-sm" autocomplete="email" autofocus>

        @error('email')
        <div class="my-2 text-red-600 dark:text-red-400" role="alert">
            <strong>{{ $message }}</strong>
        </div>
        @enderror


        <div class="mt-6">
            <x-button type="submit" rounded primary>
                {{ __('Send Password Reset Link') }}
            </x-button>
        </div>
    </form>
</div>
@endsection
