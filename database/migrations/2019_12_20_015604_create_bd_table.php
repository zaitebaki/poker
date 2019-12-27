<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // create 'user'
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->string('login')->unique();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // create 'friends'
        if (!Schema::hasTable('friends')) {
            Schema::create('friends', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user1_id')->unsigned();
                $table->integer('user2_id')->unsigned();
                $table->foreign('user1_id')->references('id')->on('users');
                $table->foreign('user2_id')->references('id')->on('users');
            });
        }

        // create 'rooms'
        if (!Schema::hasTable('rooms')) {
            Schema::create('rooms', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
            });
        }

        // create 'user_room'
        if (!Schema::hasTable('user_room')) {
            Schema::create('user_room', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->integer('room_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('room_id')->references('id')->on('rooms');
            });
        }

        // create 'invitations'
        if (!Schema::hasTable('invitations')) {
            Schema::create('invitations', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_src_user')->unsigned();
                $table->integer('id_dst_user')->unsigned();
                $table->foreign('id_src_user')->references('id')->on('users');
                $table->foreign('id_dst_user')->references('id')->on('users');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // delete constraints
        Schema::table('friends', function (Blueprint $table) {
            $table->dropForeign('friends_user1_id_foreign');
            $table->dropForeign('friends_user2_id_foreign');
        });

        Schema::table('user_room', function (Blueprint $table) {
            $table->dropForeign('user_room_room_id_foreign');
            $table->dropForeign('user_room_user_id_foreign');
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->dropForeign('invitations_id_dst_user_foreign');
            $table->dropForeign('invitations_id_src_user_foreign');
        });

        // delete tables
        Schema::dropIfExists('users');
        Schema::dropIfExists('friends');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('user_room');
        Schema::dropIfExists('invitations');
    }
}
