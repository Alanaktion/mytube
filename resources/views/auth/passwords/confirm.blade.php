@extends('layouts.app')

@section('content')
<div class="max-w-sm w-full mx-auto">
    <h2 class="mt-6 text-center text-3xl leading-9 font-bold text-gray-900 dark:text-trueGray-300">
        {{ __('Confirm Password') }}
    </h2>
    <form class="mt-8" action="{{ route('password.confirm') }}" method="POST">
        @csrf
        {{ __('Please confirm your password before continuing.') }}

        <label for="password" class="block font-semibold text-gray-700 dark:text-trueGray-300 mb-1">
            {{ __('Password') }}
        </label>
        <input id="password" name="password" type="password" required class="dark:bg-trueGray-800 dark:bg-opacity-75 border border-gray-300 dark:border-trueGray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-500 rounded py-2 px-4 block w-full placeholder-gray-500 dark:placeholder-trueGray-400 text-gray-900 dark:text-trueGray-100 appearance-none leading-normal shadow-sm" autocomplete="current-password" autofocus>

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
                <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 focus:outline-none focus:underline">
                    {{ __('Forgot Your Password?') }}
                </a>
            </div>
            @endif
        </div>
    </form>
</div>
@endsection
