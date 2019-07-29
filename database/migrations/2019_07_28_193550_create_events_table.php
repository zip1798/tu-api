<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('title')->default('');
            $table->string('place')->default('');
            $table->string('event_date')->default('');
            $table->string('image')->default('');
            $table->date('show_date')->nullable();
            $table->enum('category', ['regular', 'unregular', 'seminar'])->default('unregular');
            $table->enum('status', ['public', 'hidden', 'deleted', 'archived'])->default('public');
            $table->boolean('allow_online')->default(false);
            $table->text('brief');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('events');
    }
}
