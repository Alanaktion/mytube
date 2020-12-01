@extends('layouts.app')

@section('content')
<div class="max-w-sm w-full mx-auto">
    <h2 class="mt-6 text-center text-3xl leading-9 font-bold text-gray-900 dark:text-trueGray-300">
        {{ __('Reset Password') }}
    </h2>
    <form class="mt-8" action="{{ route('password.update') }}" method="POST">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <label for="email" class="block font-semibold text-gray-700 dark:text-trueGray-300 mb-1">
            {{ __('E-Mail Address') }}
        </label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required class="dark:bg-trueGray-800 dark:bg-opacity-75 border border-gray-300 dark:border-trueGray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-500 rounded py-2 px-4 block w-full placeholder-gray-500 dark:placeholder-trueGray-400 text-gray-900 dark:text-trueGray-100 appearance-none leading-normal shadow-sm" autocomplete="email" autofocus>

        @error('email')
        <div class="my-2 text-red-600 dark:text-red-400" role="alert">
            <strong>{{ $message }}</strong>
        </div>
        @enderror


        <label for="password" class="block font-semibold text-gray-700 dark:text-trueGray-300 mb-1">
            {{ __('Password') }}
        </label>
        <input id="password" name="password" type="password" required class="dark:bg-trueGray-800 dark:bg-opacity-75 border border-gray-300 dark:border-trueGray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-500 rounded py-2 px-4 block w-full placeholder-gray-500 dark:placeholder-trueGray-400 text-gray-900 dark:text-trueGray-100 appearance-none leading-normal shadow-sm" autocomplete="new-password">

        @error('password')
        <div class="my-2 text-red-600 dark:text-red-400" role="alert">
            <strong>{{ $message }}</strong>
        </div>
        @enderror


        <div class="mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-full">
                {{ __('Reset Password') }}
            </button>
        </div>
    </form>
</div>
@endsection
