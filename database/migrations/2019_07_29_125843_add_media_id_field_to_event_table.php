<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMediaIdFieldToEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->bigInteger('media_id')->unsigned();
            $table->dropColumn('image');

            $table->foreign('media_id')
                ->references('id')
                ->on('media')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('image')->after('event_date');

            $table->dropForeign('events_media_id_foreign');
            $table->dropColumn('media_id');
        });
    }
}
