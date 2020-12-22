<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixPublishedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Laravel and Doctrine both refuse to support timestamps, hence this.
        // https://ma.ttias.be/laravel-mysql-auto-adding-update-current_timestamp-timestamp-fields/
        // https://github.com/laravel/framework/issues/16526

        // Previously, published_at timestamps would be reset to the current
        // timestamp on every table update, which is *really* not what we want.

        DB::statement('ALTER TABLE channels CHANGE published_at published_at timestamp NULL DEFAULT CURRENT_TIMESTAMP');
        DB::statement('ALTER TABLE videos CHANGE published_at published_at timestamp NULL DEFAULT CURRENT_TIMESTAMP');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No.
    }
}
