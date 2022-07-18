@extends('layouts.app')

@section('content')
@include('channels.header', ['tab' => 'about'])
<div class="container">
    <div class="max-w-4xl">
        <dl>
            <div class="bg-slate-100 dark:bg-neutral-850 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                    {{ __('Source') }}
                </dt>
                <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                    {{ $channel->source()->getDisplayName() }}
                    @if ($channel->source_link)
                        &mdash; <a class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300" href="{{ $channel->source_link }}">{{ $channel->source_link }}</a>
                    @endif
                </dd>
            </div>
            <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                    {{ __('Published date') }}
                </dt>
                <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                    {{ $channel->published_at ? $channel->published_at->isoFormat('LL') : __('Unknown') }}
                </dd>
            </div>
            <div class="bg-slate-100 dark:bg-neutral-850 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                    {{ __('Imported date') }}
                </dt>
                <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                    {{ $channel->created_at->isoFormat('LL') }}
                </dd>
            </div>
            <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                    {{ __('Description') }}
                </dt>
                <pre class="mt-1 text-sm sm:mt-0 sm:col-span-2 whitespace-pre-wrap break-words font-sans">@description($channel->description)</pre>
            </div>
        </dl>
    </div>
</div>
@endsection
