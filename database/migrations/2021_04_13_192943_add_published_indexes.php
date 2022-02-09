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
        Schema::table('videos', function (Blueprint $table) {
            $table->index('source_type');
            $table->index('published_at');
        });
        Schema::table('playlists', function (Blueprint $table) {
            $table->index('published_at');
        });
        Schema::table('channels', function (Blueprint $table) {
            $table->index('type');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex(['source_type']);
            $table->dropIndex(['published_at']);
        });
        Schema::table('playlists', function (Blueprint $table) {
            $table->dropIndex(['published_at']);
        });
        Schema::table('channels', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['published_at']);
        });
    }
};
