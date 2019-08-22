<?php

use Illuminate\Database\Migrations\Migration;

class UpdatePollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //DB::table('polls')->where(['finished' => 1])->update(['finished' => 0]);
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
