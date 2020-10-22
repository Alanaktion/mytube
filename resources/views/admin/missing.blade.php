@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-sm uppercase font-semibold text-ngray-400 mb-2">
        Videos missing files
    </div>

    <table class="min-w-full divide-y divide-ngray-800">
        <thead>
            <tr>
                <th class="px-6 py-3 bg-ngray-800 text-left text-xs leading-4 font-medium text-ngray-300 uppercase tracking-wider">
                    Title
                </th>
                <th class="px-6 py-3 bg-ngray-800 text-left text-xs leading-4 font-medium text-ngray-300 uppercase tracking-wider">
                    Channel
                </th>
                <th class="px-6 py-3 bg-ngray-800 text-left text-xs leading-4 font-medium text-ngray-300 uppercase tracking-wider">
                    Published
                </th>
                <th class="px-6 py-3 bg-ngray-800"></th>
            </tr>
        </thead>
        <tbody class="bg-ngray-800 bg-opacity-50 divide-y divide-ngray-800">
            @forelse($videos as $video)
                <tr>
                    <td class="px-6 py-4 whitespace-no-wrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-12">
                                <div class="relative pb-9/16">
                                    <img class="absolute w-full h-full object-cover" src="/images/thumbs/{{ $video->uuid }}" alt>
                                </div>
                            </div>
                            <div class="ml-4">
                                <a href="/videos/{{ $video->uuid }}" class="leading-5 font-medium text-blue-400 hover:text-blue-300">
                                    {{ $video->title }}
                                </a>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap">
                        <a href="/channels/{{ $video->channel->uuid }}" class="leading-5 text-blue-400 hover:text-blue-300">
                            {{ $video->channel->title }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
                        {{ $video->published_at->format('F j, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap text-right leading-5 font-medium">
                        @if ($video->source_link)
                            <a href="{{ $video->source_link }}" class="text-blue-400 hover:text-blue-300">
                                Source
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-16 whitespace-no-wrap text-ngray-500 text-center">
                        No videos are missing local files.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $videos->links() }}
</div>
@endsection
