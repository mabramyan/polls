<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAnswersByPollUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
        \DB::statement("
            CREATE VIEW `answers_by_poll_users` 
            AS
            SELECT 
                au.campaign_id as campaign_id,
                au.user_id,
                au.poll_id,
                a.correct as correct,
                count(a.id) as total_answers,
                SUM(CASE WHEN a.correct=1 THEN 1 ELSE 0 END) AS correct_answers,
                SUM(CASE WHEN a.correct=1 and a.number_seven=1 THEN 1 ELSE 0 END) AS correct_number_seven
                FROM user_answers as au
                inner join answers as  a on a.id=au.answer_id
                where  au.state=1
                group by au.poll_id, au.user_id
                ;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("
        DROP VIEW IF EXISTS `answers_by_poll_users`;
    ");
    }
}
