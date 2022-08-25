<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        // These migrations should be manually applied where necessary.
        // New installations will use integer duration by default and do not require this conversion.

        // Schema::table('videos', function (Blueprint $table) {
        //     $table->unsignedInteger('duration')->nullable()->change();
        // });
        // Schema::table('video_files', function (Blueprint $table) {
        //     $table->unsignedInteger('duration')->nullable()->change();
        // });

        // // Convert existing durations to seconds
        // // MySQL converts 1:23:00 to 12300 for existing time values, which TIME() can read.
        // if (config('database.default') == 'mysql') {
        //     DB::table('videos')
        //         ->whereNotNull('duration')
        //         ->update(['duration' => DB::raw('TIME_TO_SEC(TIME(duration))')]);
        //     DB::table('video_files')
        //         ->whereNotNull('duration')
        //         ->update(['duration' => DB::raw('TIME_TO_SEC(TIME(duration))')]);
        // }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // // Convert seconds back to time format (int)
        // if (config('database.default') == 'mysql') {
        //     DB::table('videos')
        //         ->whereNotNull('duration')
        //         ->update(['duration' => DB::raw('CONVERT(SEC_TO_TIME(duration),UNSIGNED INT)')]);
        //     DB::table('video_files')
        //         ->whereNotNull('duration')
        //         ->update(['duration' => DB::raw('CONVERT(SEC_TO_TIME(duration),UNSIGNED INT)')]);
        // }

        // Schema::table('videos', function (Blueprint $table) {
        //     $table->time('duration')->nullable()->change();
        // });
        // Schema::table('video_files', function (Blueprint $table) {
        //     $table->time('duration')->nullable()->change();
        // });
    }
};
