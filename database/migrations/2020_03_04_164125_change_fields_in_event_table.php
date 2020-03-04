<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldsInEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE events CHANGE `event_date` `date` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE events CHANGE `show_date` `expire_from` date");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE events CHANGE `allow_online` `is_allow_online` tinyint(1) NOT NULL DEFAULT 0");

        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_private')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE events CHANGE `date` `event_date`  varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE events CHANGE `expire_from` `show_date` date");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE events CHANGE `is_allow_online` `allow_online` tinyint(1) NOT NULL DEFAULT 0");

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['is_approved', 'is_private']);
        });
    }
}
