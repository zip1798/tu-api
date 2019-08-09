<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultValueToNotesFieldMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `media` MODIFY `notes` TEXT DEFAULT NULL;');
//        Schema::table('media', function (Blueprint $table) {
//            $table->text('notes')->default('')->change();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `media` MODIFY `notes` TEXT NOT NULL;');
//        Schema::table('media', function (Blueprint $table) {
//            $table->text('notes')->change();
//        });
    }
}
