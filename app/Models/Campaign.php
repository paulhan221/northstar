<?php namespace Northstar\Models;

use Jenssegers\Mongodb\Model as Eloquent;
use Validator;

class Campaign extends Eloquent
{

    /**
     * Guarded attributes
     */
    protected $guarded = array('_id');

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['_id'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'reportback_id' => 'integer',
        'signup_id' => 'integer'
    ];

    /**
     * The attributes which should be stored as MongoDate objects.
     * @see https://github.com/jenssegers/laravel-mongodb#dates
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

}
