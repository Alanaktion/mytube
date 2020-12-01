@extends('layouts.app')

@section('content')
<div class="max-w-sm w-full mx-auto">
    <h2 class="mt-6 text-center text-3xl leading-9 font-bold text-gray-900 dark:text-trueGray-300">
        Sign in
    </h2>
    <form class="mt-8" action="{{ route('login') }}" method="POST">
        @csrf
        <input type="hidden" name="remember" value="true">

        <div class="rounded-md shadow-sm">
            <div class="mb-3 lg:mb-4">
                <input aria-label="Email address" name="email" type="email" required class="dark:bg-trueGray-800 dark:bg-opacity-75 border border-gray-300 dark:border-trueGray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-500 rounded py-2 px-4 block w-full placeholder-gray-500 dark:placeholder-trueGray-400 text-gray-900 dark:text-trueGray-100 appearance-none leading-normal" placeholder="Email address" value="{{ old('email') }}" autocomplete="email" autofocus>
            </div>
            <div>
                <input aria-label="Password" name="password" type="password" required class="dark:bg-trueGray-800 dark:bg-opacity-75 border border-gray-300 dark:border-trueGray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-500 rounded py-2 px-4 block w-full placeholder-gray-500 dark:placeholder-trueGray-400 text-gray-900 dark:text-trueGray-100 appearance-none leading-normal" placeholder="Password" autocomplete="current-password">
            </div>
        </div>

        @error('email')
        <div class="my-2 text-red-600 dark:text-red-400" role="alert">
            <strong>{{ $message }}</strong>
        </div>
        @enderror

        <div class="mt-6 flex items-center justify-between">
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-full">
                Sign in
            </button>
            <div class="text-sm leading-5">
                <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 focus:outline-none focus:underline">
                    Forgot your password?
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
