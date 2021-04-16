<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFavoriteChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_favorite_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()
                ->onDelete('cascade');
            $table->foreignId('channel_id')->constrained()
                ->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'channel_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_favorite_channels');
    }
}
