<?php

use Illuminate\Database\Migrations\Migration;

class DeleteUserAnswerData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('user_answers')->where(['user_id' => 1251257])->delete();
        DB::table('user_answers')->where(['user_id' => 1251277])->delete();
        DB::table('user_answers')->where(['user_id' => 1251298])->delete();
        DB::table('user_answers')->where(['user_id' => 1251251])->delete();
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
