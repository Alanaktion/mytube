@extends('layouts.app')

@section('content')
<div class="max-w-sm w-full mx-auto">
    <h2 class="mt-6 text-center text-3xl leading-9 font-bold text-ngray-300">
        Sign in
    </h2>
    <form class="mt-8" action="{{ route('login') }}" method="POST">
        <input type="hidden" name="remember" value="true">
        <div class="rounded-md shadow-sm">
            <div>
                <input aria-label="Email address" name="email" type="email" required class="bg-ngray-800 bg-opacity-75 border border-ngray-700 focus:outline-none focus:shadow-outline rounded-t-lg py-2 px-4 block w-full placeholder-ngray-400 text-ngray-100 appearance-none leading-normal" placeholder="Email address" value="{{ old('email') }}" autocomplete="email">
            </div>
            <div class="-mt-px">
                <input aria-label="Password" name="password" type="password" required class="bg-ngray-800 bg-opacity-75 border border-ngray-700 focus:outline-none focus:shadow-outline rounded-b-lg py-2 px-4 block w-full placeholder-ngray-400 text-ngray-100 appearance-none leading-normal" placeholder="Password" autocomplete="current-password">
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <button type="submit" class="bg-blue-700 hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-full">
                Sign in
            </button>
            <div class="text-sm leading-5">
                <a href="{{ route('password.request') }}" class="font-medium text-blue-400 hover:text-blue-300 focus:outline-none focus:underline transition ease-in-out duration-150">
                    Forgot your password?
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
