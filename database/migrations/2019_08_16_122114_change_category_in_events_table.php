<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCategoryInEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE events MODIFY category ENUM('regular', 'unregular', 'seminar', 'other') NOT NULL");

        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_open_registration')->default(false);
            $table->text('registration_fields');
            $table->text('html_after_registration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE events MODIFY category ENUM('regular', 'unregular', 'seminar') NOT NULL");

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['is_open_registration', 'registration_fields', 'html_after_registration']);
        });
    }
}
