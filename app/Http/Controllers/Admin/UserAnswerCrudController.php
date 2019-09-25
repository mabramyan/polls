<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\UserAnswerRequest as StoreRequest;
use App\Http\Requests\UserAnswerRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
use App\Models\UserAnswer;
use Symfony\Component\Console\Input\Input;
use App\Http\Resources\UserAnswer as UserAnswerResource;

/**
 * Class UserAnswerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class UserAnswerCrudController extends CrudController
{
    public function setup()
    {
        /* 
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\UserAnswer');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/user_answer');
        $this->crud->setEntityNameStrings('UserAnswer', 'UserAnswers');

        $this->crud->denyAccess('create');
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('update');
        $this->crud->removeAllButtonsFromStack('line');

        $t = request()->input('state');
        if (!isset($t)) {
            $this->crud->addClause('where', 'user_answers.state', 1);
        }
        $this->crud->addClause('JOIN', 'polls', 'user_answers.poll_id', '=', 'polls.id');
        $this->crud->addClause('JOIN', 'questions', 'user_answers.question_id', '=', 'questions.id');
        $this->crud->addClause('JOIN', 'campaigns', 'user_answers.campaign_id', '=', 'campaigns.id');
        $this->crud->addClause('SELECT', 'user_answers.*');


        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        // $this->crud->setFromDb();
        $this->crud->addColumn(
            [
                'name' => 'id',
                'label' => 'ID',
                'type' => 'text',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhere(\DB::raw('user_answers.id'), 'like', '%'.$searchTerm.'%');
                }
            ]
        );
        $this->crud->addColumn(
            [
                'name' => 'user_id',
                'label' => 'User ID',
                'type' => 'text',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhere(\DB::raw('user_answers.user_id'), 'like', '%'.$searchTerm.'%');
                }
            ]
        );
        $this->crud->addColumn(
            [
                // 1-n relationship
                'label' => "Campaign", // Table column heading
                'type' => "select",
                'name' => 'user_answers.campaign_id', // the column that contains the ID of that connected entity;
                'entity' => 'campaign', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Campaign", // foreign key model
            ]
        );
        $this->crud->addColumn(
            [
                // 1-n relationship
                'label' => "Poll", // Table column heading
                'type' => "select",
                'name' => 'user_answers.poll_id', // the column that contains the ID of that connected entity;
                'entity' => 'poll', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Poll", // foreign key model
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('poll', function ($q) use ($column, $searchTerm) {
                        $q->where('name', 'like', '%'.$searchTerm.'%');
                    });
                }
            ]
        );

        $this->crud->addColumn(
            [
                // 1-n relationship
                'label' => "Question", // Table column heading
                'type' => "select",
                'name' => 'user_answers.question_id', // the column that contains the ID of that connected entity;
                'entity' => 'question', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Question", // foreign key model
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('question', function ($q) use ($column, $searchTerm) {
                        $q->where('name', 'like', '%'.$searchTerm.'%');
                    });
                }
            ]
        );
        $this->crud->addColumn(
            [
                // 1-n relationship
                'label' => "Answer", // Table column heading
                'type' => "select",
                'name' => 'user_answers.answer_id', // the column that contains the ID of that connected entity;
                'entity' => 'answer', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Answer", // foreign key model
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('answer', function ($q) use ($column, $searchTerm) {
                        $q->where('name', 'like', '%'.$searchTerm.'%');
                    });
                }
            ]
        );


        $this->crud->addColumn(
            [
                'name' => "created_at",
                'label' => "Created",
                'type' => "date",
            ]
        );


        //filters
        $this->crud->addFilter(
            [ // simple filter
                'type' => 'text',
                'name' => 'user_id',
                'label' => 'User ID'
            ],
            false,
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'user_answers.user_id', 'LIKE', "%$value%");
            }
        );


        $this->crud->addFilter([ // select2 filter
            'name' => 'campaign_id',
            'type' => 'select2',
            'label' => 'Campaign'
        ], function () {
            return \App\Models\Campaign::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'user_answers.campaign_id', $value);
        });

        $this->crud->addFilter([ // select2 filter
            'name' => 'poll_id',
            'type' => 'select2',
            'label' => 'Poll'
        ], function () {
            return \App\Models\Poll::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'user_answers.poll_id', $value);
        });

        $this->crud->addFilter([ // select2 filter
            'name' => 'question_id',
            'type' => 'select2',
            'label' => 'Question'
        ], function () {
            return \App\Models\Question::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'question_id', $value);
        });


        $this->crud->addFilter([ // select2 filter
            'name' => 'answer_id',
            'type' => 'select2',
            'label' => 'Answer'
        ], function () {
            return \App\Models\Campaign::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'answer_id', $value);
        });

        $this->crud->addFilter(
            [ // daterange filter
                'type' => 'date_range',
                'name' => 'user_answers.from_to',
                'label' => 'Date range'
            ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value);
                $this->crud->addClause('where', 'user_answers.created_at', '>=', $dates->from);
                $this->crud->addClause('where', 'user_answers.created_at', '<=', $dates->to . ' 23:59:59');
            }
        );
        $this->crud->addFilter([ // select2 filter
            'name' => 'user_answers.state',
            'type' => 'select2',
            'label' => 'Published'
        ], function () {
            return [

                0 => 'All',
                1 => 'Active',

            ];
        }, function ($value) { // if the filter is active
            if ($value > 0) {
                $this->crud->addClause('where', 'user_answers.state', ($value));
            }
        });



        // add asterisk for fields that are required in UserAnswerRequest
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


    public function getUserAnsers($pollId)
    {


        return response()->json(['success' => UserAnswerResource::collection(UserAnswer::where([['poll_id', $pollId], ['user_answers.state', 1]])
            ->select(['user_answers.*', 'answers.correct', 'answers.number_seven'])
            ->join('answers', 'answers.id', '=', 'user_answers.answer_id')
            ->get())], 200);
    }
    public function getTotalReport($campaignId)
    {
        if (empty($campaignId)) {
            return response()->json(['success' => false], 200);
        }


        $res = \DB::select(\DB::raw("SELECT 
        p.id as p_id,p.name,
                                ag.* ,
                                count(ag.user_id) as users,
                                SUM(CASE WHEN ag.correct_answers>4 or ag.correct_number_seven>0 THEN 1 ELSE 0 END) AS winners,
                                SUM(CASE WHEN ag.correct_number_seven>0 THEN 1 ELSE 0 END) AS correct_number_seven,
                                SUM(CASE WHEN ag.correct_answers=7 THEN 1 ELSE 0 END) AS correct_answers_7,
                                SUM(CASE WHEN ag.correct_answers=6 THEN 1 ELSE 0 END) AS correct_answers_6,
                                SUM(CASE WHEN ag.correct_answers=5 THEN 1 ELSE 0 END) AS correct_answers_5,
                                SUM(CASE WHEN ag.correct_answers=4 THEN 1 ELSE 0 END) AS correct_answers_4,
                                SUM(CASE WHEN ag.correct_answers=3 THEN 1 ELSE 0 END) AS correct_answers_3,
                                SUM(CASE WHEN ag.correct_answers=2 THEN 1 ELSE 0 END) AS correct_answers_2,
                                SUM(CASE WHEN ag.correct_answers=1 THEN 1 ELSE 0 END) AS correct_answers_1
                            FROM polls as p
                            left join  answers_by_poll_users as ag on ag.poll_id=p.id
                                where ag.campaign_id=:campaign_id
                            group by p.id
                            ; 
        "), array(
            'campaign_id' => $campaignId,
        ));
        return response()->json(['success' => $res], 200);
    }
    public function getTotalReportSummary($campaignId)
    {
        if (empty($campaignId)) {
            return response()->json(['success' => false], 200);
        }
 
        $totalUnique =   \DB::select(\DB::raw('select count(*) as cnt from (select count(*) from answers_by_poll_users where campaign_id='.$campaignId.' group by answers_by_poll_users.user_id) as t ;'));
           
        

        $res = \DB::select(\DB::raw("SELECT 
        p.id as p_id,p.name,
                                ag.* ,
                                count(ag.user_id) as users,
                                SUM(CASE WHEN ag.correct_answers>4 THEN 1 ELSE 0 END) AS winners,
                                SUM(CASE WHEN ag.correct_number_seven>0 THEN 1 ELSE 0 END) AS correct_number_seven,
                                SUM(CASE WHEN ag.correct_answers=7 THEN 1 ELSE 0 END) AS correct_answers_7,
                                SUM(CASE WHEN ag.correct_answers=6 THEN 1 ELSE 0 END) AS correct_answers_6,
                                SUM(CASE WHEN ag.correct_answers=5 THEN 1 ELSE 0 END) AS correct_answers_5,
                                SUM(CASE WHEN ag.correct_answers=4 THEN 1 ELSE 0 END) AS correct_answers_4,
                                SUM(CASE WHEN ag.correct_answers=3 THEN 1 ELSE 0 END) AS correct_answers_3,
                                SUM(CASE WHEN ag.correct_answers=2 THEN 1 ELSE 0 END) AS correct_answers_2,
                                SUM(CASE WHEN ag.correct_answers=1 THEN 1 ELSE 0 END) AS correct_answers_1
                            FROM polls as p
                            left join  answers_by_poll_users as ag on ag.poll_id=p.id
                                where ag.campaign_id=:campaign_id
                            group by ag.campaign_id
                            ; 
        "), array(
            'campaign_id' => $campaignId,
        ));
        if (!empty($res)) {
            if(!empty($totalUnique))
            {
                $res[0]->totalUnique = $totalUnique[0]->cnt;
            }
            
            return response()->json(['success' => $res[0]], 200);
        }
        return response()->json(['success' => false], 200);
    }
}
