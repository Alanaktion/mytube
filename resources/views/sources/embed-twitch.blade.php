<div class="relative mb-4 lg:mb-6 pb-9/16">
    <iframe class="absolute w-full h-full"
        src="https://player.twitch.tv/?video={{ $video->uuid }}&amp;parent={{ request()->getHost() }}"
        referrerpolicy="no-referrer"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen></iframe>
</div>
