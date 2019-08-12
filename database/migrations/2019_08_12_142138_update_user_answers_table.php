<?php

use Illuminate\Database\Migrations\Migration;

class UpdateUserAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('user_answers')->where(['correct' => 1])->update(['correct' => null]);
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
