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
        Schema::table('videos', function (Blueprint $table) {
            $table->time('duration')->nullable()->after('source_visibility');
        });

        // Populate video duration from local files where available.
        if (config('database.default') == 'mysql') {
            DB::table('videos')
                ->join('video_files', 'video_files.video_id', '=', 'videos.id')
                ->whereNotNull('video_files.duration')
                ->update([
                    'videos.duration' => DB::raw('video_files.duration'),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
};
