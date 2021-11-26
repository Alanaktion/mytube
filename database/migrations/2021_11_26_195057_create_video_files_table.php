<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateVideoFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained();
            $table->string('path')->unique();
            $table->string('mime_type');
            $table->timestamps();
        });

        DB::table('videos')
            ->whereNotNull('file_path')
            ->orderBy('id')
            ->chunk(50, function ($videos) {
                foreach ($videos as $video) {
                    DB::table('video_files')->insert([
                        'video_id' => $video->id,
                        'path' => $video->file_path,
                        'mime_type' => mime_content_type($video->file_path),
                        'created_at' => $video->created_at,
                    ]);
                }
            });

        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        throw new Exception('This migration cannot be reversed.');
    }
}
