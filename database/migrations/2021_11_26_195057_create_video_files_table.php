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
                    if (is_file($video->file_path)) {
                        $mimeType = mime_content_type($video->file_path);
                    } else {
                        $mimeType = 'video/' . pathinfo($video->file_path, PATHINFO_EXTENSION);
                        if ($mimeType == 'm4v') {
                            $mimeType = 'video/mp4';
                        }
                    }
                    DB::table('video_files')->insert([
                        'video_id' => $video->id,
                        'path' => $video->file_path,
                        'mime_type' => $mimeType,
                        'created_at' => $video->created_at,
                        'updated_at' => $video->updated_at,
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
};
