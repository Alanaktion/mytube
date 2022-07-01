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
        Schema::table('video_files', function (Blueprint $table) {
            $table->after('mime_type', function (Blueprint $table) {
                $table->unsignedBigInteger('size')->nullable();
                $table->unsignedInteger('width')->nullable();
                $table->unsignedInteger('height')->nullable();
                $table->time('duration')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_files', function (Blueprint $table) {
            $table->dropColumn('size');
            $table->dropColumn('width');
            $table->dropColumn('height');
            $table->dropColumn('duration');
        });
    }
};
