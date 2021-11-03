<?php

namespace App\GraphQL\Mutations;

use App\Models\UserFavoriteVideo;
use App\Models\Video;
use Closure;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Mutation;

class SetFavoriteVideoMutation extends Mutation
{
    /** @var array<string, string> */
    protected $attributes = [
        'name' => 'setFavoriteVideo',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function args(): array
    {
        return [
            'uuid' => [
                'name' => 'uuid',
                'description' => 'Video UUID',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'exists:videos,uuid'],
            ],
            'favorite' => [
                'name' => 'favorite',
                'type' => Type::nonNull(Type::boolean()),
                'rules' => ['required'],
            ],
        ];
    }

    /**
     * @param object $root
     * @param array<string, mixed> $args
     * @param mixed $context
     * @return mixed
     */
    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $video = Video::where('uuid', $args['uuid'])->first();
        if (!$video) {
            return null;
        }

        /** @var \App\Models\User */
        $user = Auth::user();

        if ($args['favorite']) {
            UserFavoriteVideo::firstOrCreate([
                'user_id' => $user->id,
                'video_id' => $video->id,
            ]);
            return true;
        }

        $favorites = $user->favoriteVideos();
        $favorites->detach($video->id);
        return false;
    }
}
