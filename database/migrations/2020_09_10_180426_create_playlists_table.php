<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // https://developers.google.com/youtube/v3/docs/playlists#resource-representation
        Schema::create('playlists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('channel_id')->nullable();
            $table->foreign('channel_id')
                ->references('id')->on('channels');
            $table->string('uuid')->unique();
            $table->string('title');
            $table->text('description');
            $table->timestamp('published_at');
            $table->timestamps();
        });
        // https://developers.google.com/youtube/v3/docs/playlistItems#resource-representation
        Schema::create('playlist_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('playlist_id')->nullable();
            $table->foreign('playlist_id')
                ->references('id')->on('playlists');
            $table->unsignedBigInteger('video_id')->nullable();
            $table->foreign('video_id')
                ->references('id')->on('videos');
            $table->string('uuid')->nullable()->unique();
            $table->unsignedInteger('position');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('playlist_items');
        Schema::dropIfExists('playlists');
    }
};
