<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('users_meeting_rooms', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id', false, true);
            $table->bigInteger('meeting_room_id', false, true);
            $table->date('occupy_at');
            $table->time('start_at');
            $table->time('end_at');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')->on('users');
            $table->foreign('meeting_room_id')
                ->references('id')->on('meeting_rooms');

            /* A room can't be double booked. */
            $table->unique(['meeting_room_id', 'occupy_at', 'start_at']);

        });

        /* Enable api authentication. */
        Schema::table('users', function ($table) {
            $table->string('api_token', 80)->after('password')
                ->unique()
                ->nullable()
                ->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_meeting_rooms');
        Schema::dropIfExists('meeting_rooms');
        Schema::table('users', function ($table) {
            $table->dropColumn('api_token');
        });
    }
}
