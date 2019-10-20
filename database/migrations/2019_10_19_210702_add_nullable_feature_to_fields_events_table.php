<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullableFeatureToFieldsEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE `events` MODIFY `registration_fields` TEXT COLLATE `utf8mb4_unicode_ci` NULL;');
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE `events` MODIFY `html_after_registration` TEXT COLLATE `utf8mb4_unicode_ci` NULL;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `events` MODIFY `registration_fields` TEXT COLLATE `utf8mb4_unicode_ci` NOT NULL;');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `events` MODIFY `html_after_registration` TEXT COLLATE `utf8mb4_unicode_ci` NOT NULL;');
    }
}
