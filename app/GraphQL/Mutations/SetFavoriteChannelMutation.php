<?php

namespace App\GraphQL\Mutations;

use App\Models\Channel;
use App\Models\UserFavoriteChannel;
use Closure;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Mutation;

class SetFavoriteChannelMutation extends Mutation
{
    protected $attributes = [
        'name' => 'setFavoriteChannel',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'uuid' => [
                'name' => 'uuid',
                'description' => 'Channel UUID',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'exists:channel,uuid'],
            ],
            'favorite' => [
                'name' => 'favorite',
                'type' => Type::nonNull(Type::boolean()),
                'rules' => ['required'],
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $channel = Channel::where('uuid', $args['uuid'])->first();
        if (!$channel) {
            return null;
        }

        /** @var \App\Models\User */
        $user = Auth::user();

        if ($args['favorite']) {
            UserFavoriteChannel::firstOrCreate([
                'user_id' => $user->id,
                'channel_id' => $channel->id,
            ]);
            return true;
        }

        $favorites = $user->favoriteChannels();
        $favorites->detach($channel->id);
        return false;
    }
}
