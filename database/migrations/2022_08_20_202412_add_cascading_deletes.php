<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('database.default') != 'sqlite') {
            Schema::table('playlist_items', function (Blueprint $table) {
                $table->dropForeign(['playlist_id']);
                $table->foreign('playlist_id')
                    ->references('id')->on('playlists')->onDelete('cascade');
            });
            Schema::table('video_files', function (Blueprint $table) {
                $table->dropForeign(['video_id']);
                $table->foreignId('video_id')->constrained()->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
