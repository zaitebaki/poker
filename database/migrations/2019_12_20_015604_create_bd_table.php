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
                $table->integer('victory')->unsigned()->default(0);
                $table->integer('gameover')->unsigned()->default(0);
                $table->integer('balance')->default(0);
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

        // create 'payments'
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->integer('opponent_user_id')->unsigned();
                $table->integer('value');
                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('opponent_user_id')->references('id')->on('users');
            });
        }

        // create 'user_room'
        if (!Schema::hasTable('user_payment')) {
            Schema::create('user_payment', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->integer('payment_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('payment_id')->references('id')->on('payments');
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

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_opponent_user_id_foreign');
            $table->dropForeign('payments_user_id_foreign');
        });

        Schema::table('user_payment', function (Blueprint $table) {
            $table->dropForeign('user_payment_payment_id_foreign');
            $table->dropForeign('user_payment_user_id_foreign');
        });

        // delete tables
        Schema::dropIfExists('users');
        Schema::dropIfExists('friends');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('user_payment');
    }
}
