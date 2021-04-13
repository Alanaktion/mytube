<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Video;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GraphQLTest extends TestCase
{
    public function testQueryVideos()
    {
        $response = $this->postJson('/graphql', [
            'query' => <<<GQL
                {
                    videos {
                        data {
                            uuid,
                            title,
                            channel {
                                title,
                                type
                            }
                        }
                        per_page,
                        total
                    }
                }
            GQL
        ]);
        $response->assertStatus(200);
        $this->assertVideoResponse($response);
        $this->assertGreaterThanOrEqual(5, $response->json('data.videos.total'));
    }

    public function testQueryVideoByUuid()
    {
        $video = Video::first();
        $response = $this->postJson('/graphql', [
            'query' => <<<GQL
                {
                    videos(uuid: "{$video->uuid}") {
                        data {
                            uuid,
                            title,
                            channel {
                                title,
                                type
                            }
                        }
                        per_page,
                        total
                    }
                }
            GQL
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('data.videos.total', 1);
        $response->assertJsonPath('data.videos.data.0.uuid', $video->uuid);
    }

    public function testQueryVideoByTitle()
    {
        $video = Video::first();
        $response = $this->postJson('/graphql', [
            'query' => <<<GQL
                {
                    videos(search: "{$video->title}") {
                        data {
                            uuid,
                            title,
                            channel {
                                title,
                                type
                            }
                        }
                        per_page,
                        total
                    }
                }
            GQL
        ]);
        $response->assertStatus(200);
        $this->assertVideoResponse($response);
        // TODO: update this to check that the requested video is included in
        // the results, but not necessarily the first result.
    }

    public function testQueryVideoByChannel()
    {
        $channel = Channel::whereHas('videos')->first();
        $response = $this->postJson('/graphql', [
            'query' => <<<GQL
                {
                    videos(channel_id: {$channel->id}) {
                        data {
                            uuid,
                            title,
                            channel {
                                title,
                                type
                            }
                        }
                        per_page,
                        total
                    }
                }
            GQL
        ]);
        $response->assertStatus(200);
        $this->assertVideoResponse($response);
        $response->assertJsonPath('data.videos.data.0.channel.title', $channel->title);
    }

    /**
     * Assert that a GraphQL response contains the test video structure.
     */
    protected function assertVideoResponse(\Illuminate\Testing\TestResponse $response): void
    {
        $response->assertJson(function (AssertableJson $json) {
            $json->whereAllType([
                'data' => 'array',
                'data.videos' => 'array',
                'data.videos.data' => 'array',
                'data.videos.data.0.uuid' => 'string',
                'data.videos.data.0.title' => 'string',
                'data.videos.data.0.channel.title' => 'string',
                'data.videos.data.0.channel.type' => 'string',
                'data.videos.per_page' => 'integer',
                'data.videos.total' => 'integer',
            ]);
        });
    }

    public function testPagination()
    {
        $response = $this->postJson('/graphql', [
            'query' => <<<GQL
                {
                    videos(page: 2) {
                        current_page,
                        from
                    }
                }
            GQL
        ]);
        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('data.videos.current_page'));
        $this->assertGreaterThanOrEqual(5, $response->json('data.videos.from'));
    }

    public function testQueryChannels()
    {
        $response = $this->postJson('/graphql', [
            'query' => <<<GQL
                {
                    channels {
                        data {
                            uuid,
                            title
                        }
                        per_page,
                        total
                    }
                }
            GQL
        ]);
        $response->assertStatus(200);
        $this->assertChannelResponse($response);
        $this->assertGreaterThanOrEqual(5, $response->json('data.channels.total'));
    }

    public function testQueryChannelByUuid()
    {
        $channel = Channel::first();
        $response = $this->postJson('/graphql', [
            'query' => <<<GQL
                {
                    channels(uuid: "{$channel->uuid}") {
                        data {
                            uuid,
                            title
                        }
                        per_page,
                        total
                    }
                }
            GQL
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('data.channels.total', 1);
        $response->assertJsonPath('data.channels.data.0.uuid', $channel->uuid);
    }

    /**
     * Assert that a GraphQL response contains the test channel structure.
     */
    protected function assertChannelResponse(\Illuminate\Testing\TestResponse $response): void
    {
        $response->assertJson(function (AssertableJson $json) {
            $json->whereAllType([
                'data' => 'array',
                'data.channels' => 'array',
                'data.channels.data' => 'array',
                'data.channels.data.0.uuid' => 'string',
                'data.channels.data.0.title' => 'string',
                'data.channels.per_page' => 'integer',
                'data.channels.total' => 'integer',
            ]);
        });
    }
}
