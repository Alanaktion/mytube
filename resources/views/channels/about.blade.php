@extends('layouts.app')

@section('content')
<div class="container">
    @include('channels.header', ['tab' => 'about'])

    <div class="max-w-4xl">
        <dl>
            <div class="bg-gray-100 dark:bg-trueGray-850 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 dark:text-trueGray-400">
                    {{ __('Source') }}
                </dt>
                <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                    {{ $channel->source()->getDisplayName() }}
                    @if ($channel->source_link)
                        &mdash; <a class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300" href="{{ $channel->source_link }}">{{ $channel->source_link }}</a>
                    @endif
                </dd>
            </div>
            <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 dark:text-trueGray-400">
                    {{ __('Published date') }}
                </dt>
                <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                    {{ $channel->published_at ? $channel->published_at->translatedFormat('F j, Y') : __('Unknown') }}
                </dd>
            </div>
            <div class="bg-gray-100 dark:bg-trueGray-850 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 dark:text-trueGray-400">
                    {{ __('Imported date') }}
                </dt>
                <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                    {{ $channel->created_at->translatedFormat('F j, Y') }}
                </dd>
            </div>
            <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 dark:text-trueGray-400">
                    {{ __('Description') }}
                </dt>
                <pre class="mt-1 text-sm sm:mt-0 sm:col-span-2 whitespace-pre-wrap font-sans">{{ $channel->description }}</pre>
            </div>
        </dl>
    </div>
</div>
@endsection
