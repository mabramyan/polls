<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CampaignRequest as StoreRequest;
use App\Http\Requests\CampaignRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
use App\Http\Resources\Campaign as CampaignResource;
use App\Http\Resources\Poll as PollResource;
use App\Models\Campaign;
use App\Models\Poll;

/**
 * Class CampaignCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CampaignCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Campaign');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/campaign');
        $this->crud->setEntityNameStrings('campaign', 'campaigns');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        $this->crud->setFromDb();

        // add asterisk for fields that are required in CampaignRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function getCampaings()
    {



        return response()->json([
            'success' => ['campaigns'=>CampaignResource::collection(Campaign::all()),
            'polls'=>PollResource::collection(Poll::all())
            ]
        ], 200);




    }
    public function getReport()
    {
        die(env('DB_HOST', '127.0.0.1'));

        $res = \DB::statement( "
        SET @col = NULL;
        SET @sql = NULL;
        
        SELECT GROUP_CONCAT(DISTINCT
            CONCAT(
              'max(case when s1.poll_id=' , id , ' then s1.poll_id else null end) round_' ,id
              ,',sum(case when s1.poll_id=' ,id ,' then s1.correct else null end) correct_answers_',id
              ,',max(case when s1.poll_id=' ,id ,' then s1.created_at else null end) min_prediction_date_',id
            )
          ) INTO @col
        FROM sports.polls
        where finished=1;
        
        set @sql = concat('
        
        with users_answers as(
            SELECT a.user_id ,a.poll_id ,a.question_id
                -- ,first_value(a.question_id) over(partition by a.user_id ,a.poll_id order by a.state desc) question_id
                -- ,first_value(a.created_at) over(partition by a.user_id ,a.poll_id order by a.created_at asc) created_at
                ,max(case when a.state=1 then a.answer_id else null end) answer_id
                ,min(a.created_at) over(partition by a.user_id ,a.poll_id) created_at
            FROM sports.user_answers a
            where a.campaign_id =1
            group by a.user_id ,a.poll_id ,a.question_id
        )
        , s1 as(
            select users_answers.user_id ,users_answers.poll_id ,users_answers.created_at
                ,sum(correct) correct
            from users_answers
            left join sports.answers ans on ans.id=users_answers.answer_id
            group by users_answers.user_id ,users_answers.poll_id ,users_answers.created_at
        )
        select s1.user_id
            ,' , @col ,'
        from s1
        group by s1.user_id');
        
        prepare stmt from @sql;
        execute stmt;
        deallocate prepare stmt;
        
        " );
die($res);

        $res = \DB::select("
        SET @col = NULL;
        SET @sql = NULL;
        
        SELECT GROUP_CONCAT(DISTINCT
            CONCAT(
              'max(case when s1.poll_id=' , id , ' then s1.poll_id else null end) round_' ,id
              ,',sum(case when s1.poll_id=' ,id ,' then s1.correct else null end) correct_answers_',id
              ,',max(case when s1.poll_id=' ,id ,' then s1.created_at else null end) min_prediction_date_',id
            )
          ) INTO @col
        FROM polls
        where finished=1;
        
        set @sql = concat('
        
        with users_answers as(
            SELECT a.user_id ,a.poll_id ,a.question_id
                -- ,first_value(a.question_id) over(partition by a.user_id ,a.poll_id order by a.state desc) question_id
                -- ,first_value(a.created_at) over(partition by a.user_id ,a.poll_id order by a.created_at asc) created_at
                ,max(case when a.state=1 then a.answer_id else null end) answer_id
                ,min(a.created_at) over(partition by a.user_id ,a.poll_id) created_at
            FROM user_answers a
            where a.campaign_id =1
            group by a.user_id ,a.poll_id ,a.question_id
        )
        , s1 as(
            select users_answers.user_id ,users_answers.poll_id ,users_answers.created_at
                ,sum(correct) correct
            from users_answers
            left join answers ans on ans.id=users_answers.answer_id
            group by users_answers.user_id ,users_answers.poll_id ,users_answers.created_at
        )
        select s1.user_id
            ,' , @col ,'
        from s1
        group by s1.user_id');
        
        prepare stmt from @sql;
        execute stmt;
        deallocate prepare stmt;");
    }
}
