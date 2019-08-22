<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteTestUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('user_answers')->where(['user_id' => 1251435])->delete();
        DB::table('user_answers')->where(['user_id' => 1251427])->delete();
        DB::table('user_answers')->where(['user_id' => 1251425])->delete();
        DB::table('user_answers')->where(['user_id' => 1251424])->delete();
        DB::table('user_answers')->where(['user_id' => 1251367])->delete();
        DB::table('user_answers')->where(['user_id' => 1251299])->delete();
        DB::table('user_answers')->where(['user_id' => 1251277])->delete();
        DB::table('user_answers')->where(['user_id' => 1251257])->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
