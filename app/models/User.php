<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Jenssegers\Mongodb\Model as Eloquent;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $primaryKey = "_id";

	/**
	 * The database collection used by the model.
	 *
	 * @var string
	 */
	protected $collection = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	private $rules = array(
	    'email'=>'email|unique:users',
	    'mobile'=>'unique:users',
	    'password'=>'required'
    );

    /**
	 * Authentication rules
	 *
	 * @var array
	*/
	private $auth_rules = array(
	    'email'=>'email',
	    'password'=>'required'
    );

	private $messages;

	/**
	 * Determines validation rules for user registration and authentication
	 *
	 * @var array
	 */
    public function validate($data, $auth = false)
    {	
    	$rules = ($auth == true) ? $this->auth_rules : $this->rules;

        $v = Validator::make($data, $rules);

        $v->sometimes('email', 'required', function($data)
		{
			$mobile = (empty($data->mobile)) ? true : false;
		    return $mobile;
		});

		$v->sometimes('mobile', 'required', function($data)
		{
			$email = (empty($data->email)) ? true : false;
		    return $email;
		});

        if($v->fails())
        {
            $this->messages = $v->messages()->all();
            return false;
        }

        return true;
    }

    public function messages()
    {
        return $this->messages;
    }

    /**
    * Email address mutator that converts the email value to lowercase
    *
    */
    public function setEmailAttribute($value) {
    	$this->attributes['email'] = strtolower($value);
    }

    /**
    * Passoword mutator that hashes the password field
    *
    */
    public function setPasswordAttribute($value) {
    	$this->attributes['password'] = Hash::make($value);
    }

	/*
	* Automatically convert date columns to instances of Carbon
	*
	*/
	public function getDates()
	{
		return array('created_at','updated_at');
	}

    /**
    * Define embedded relationship with the Campaign Model
    *
    */
    public function campaigns() {
    	return $this->embedsMany('Campaign');
    }

	/**
	* Generate a token to authenticate a user
	*
	* @return mixed
	*/
	public function login() {
		$token = Token::getInstance();
		$token->user_id	= $this->_id;
		$token->save();
		
		return $token;
	}

}
