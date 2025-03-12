@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-sm uppercase font-semibold text-slate-500 dark:text-neutral-400 mb-2 lg:mb-3">
        {{ __('Queued Actions') }}
    </div>

    <div id="app-job-details">
        <job-details></job-details>
    </div>

    <table class="min-w-full divide-y divide-slate-200 dark:divide-neutral-800 shadow-sm overflow-hidden sm:rounded-lg mb-4">
        <thead>
            <tr>
                <th class="px-6 py-3 bg-slate-100 dark:bg-neutral-800 text-left text-xs leading-4 font-medium text-slate-500 dark:text-neutral-300 uppercase tracking-wider">
                    {{ __('Queue') }}
                </th>
                <th class="px-6 py-3 bg-slate-100 dark:bg-neutral-800 text-left text-xs leading-4 font-medium text-slate-500 dark:text-neutral-300 uppercase tracking-wider">
                    {{ __('Action') }}
                </th>
                <th class="px-6 py-3 bg-slate-100 dark:bg-neutral-800 text-left text-xs leading-4 font-medium text-slate-500 dark:text-neutral-300 uppercase tracking-wider">
                    {{ __('Attempts') }}
                </th>
                <th class="px-6 py-3 bg-slate-100 dark:bg-neutral-800 text-left text-xs leading-4 font-medium text-slate-500 dark:text-neutral-300 uppercase tracking-wider">
                    {{ __('Started') }}
                </th>
                <th class="px-6 py-3 bg-slate-100 dark:bg-neutral-800 text-left text-xs leading-4 font-medium text-slate-500 dark:text-neutral-300 uppercase tracking-wider">
                    {{ __('Queued') }}
                </th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-neutral-850 divide-y divide-slate-200 dark:divide-neutral-800">
            @forelse($queue as $action)
                <tr>
                    <td class="px-6 py-2">
                        {{ $action->queue }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $action->name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $action->attempts }}
                    </td>
                    <td class="px-6 py-4">
                        {{ !empty($action->reserved_at) ? $action->reserved_at->diffForHumans() : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ !empty($action->available_at) ? $action->available_at->diffForHumans() : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-slate-500 dark:text-neutral-500 text-center">
                        {{ __('No queued actions available.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
