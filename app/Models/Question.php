<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Question extends Model
{
    use CrudTrait;

    protected $defaults = array(
        'start_date' => '',
        'end_date' => '',
        'team_1' => '',
        'team_2' => '',
    );
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'questions';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name', 'poll_id', 'start_date', 'end_date', 'state','image'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function __construct(array $attributes = array())
    {
        $this->defaults = array(
            'start_date' => date('Y-m-d H:i:s'),
            'end_date' => date('Y-m-d H:i:s'),
        );

        $this->setRawAttributes($this->defaults, true);
        parent::__construct($attributes);
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function poll()
    {
        return $this->belongsTo('App\Models\Poll');
    }
    public function answers()
    {
        return $this->hasMany('App\Models\Answer')->orderBy('lft');
    }
    public function team1()
    {
        return $this->belongsTo('App\Models\Poll','team_1');
    }
    public function team2()
    {
        return $this->belongsTo('App\Models\Poll','team_2');
    }

    public function user_answers()
    {
        return $this->hasMany('App\Models\UserAnswer');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    public function setStartDateAttribute($value)
    {

        $this->attributes['start_date'] = empty($value) ? date('Y-m-d H:i:s') : $value;
    }
    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = empty($value) ? date('Y-m-d H:i:s') : $value;
    }
    public function setImageAttribute($value)
    {
        $attribute_name = "image";
        $disk ='public_uploads';// config('backpack.base.root_disk_name');
        $destination_path = "questions";

        // if the image was erased
        if ($value==null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }
     
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
        $public_destination_path='uploads/'.$this->attributes[$attribute_name];
        $this->attributes[$attribute_name] =$public_destination_path;

        




        // // if a base64 was sent, store it in the db
        // if (starts_with($value, 'data:image'))
        // {
        //     // 0. Make the image
        //     $image = \Image::make($value)->encode('jpg', 90);
        //     // 1. Generate a filename.
        //     $filename = md5($value.time()).'.jpg';
        //     // 2. Store the image on disk.
        //     \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());
        //     // 3. Save the path to the database


        //     $public_destination_path = \Str::replaceFirst('public/', '', $destination_path);


        //     $this->attributes[$attribute_name] = $public_destination_path . '/' . $filename;

        // }
    }
    
}
