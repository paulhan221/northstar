<?php namespace Northstar\Models;

use Jenssegers\Mongodb\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Request;
use Hash;
use Validator;

/**
 * Class User
 *
 * @method static where()
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

  use Authenticatable, CanResetPassword;

  protected $fillable =
    ['email', 'mobile', 'password',
      'first_name', 'last_name', 'birthdate', 'interests',
      'race', 'religion',
      'college_name', 'degree_type', 'major_name', 'hs_gradyear', 'hs_name', 'sat_math', 'sat_verbal', 'sat_writing',
      'addr_street1', 'addr_street2', 'addr_city', 'addr_state', 'addr_zip', 'country',
      'cgg_id', 'drupal_id', 'agg_id', 'source'
    ];

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
  protected $hidden = ['password'];

  /**
   * Validation rules
   *
   * @var array
   */
  private $rules = [
    'email' => 'email|unique:users',
    'mobile' => 'unique:users'
  ];

  /**
   * Authentication rules
   *
   * @var array
   */
  private $auth_rules = [
    'email' => 'email',
    'password' => 'required'
  ];

  private $messages;

  /**
   * Display validation messages
   *
   */
  public function messages()
  {
    return $this->messages;
  }

  /**
   * Email address mutator that converts the email value to lowercase
   *
   */
  public function setEmailAttribute($value)
  {
    $this->attributes['email'] = strtolower($value);
  }

  /**
   * Password mutator that hashes the password field
   *
   */
  public function setPasswordAttribute($value)
  {
    $this->attributes['password'] = Hash::make($value);
  }

  /**
   * Automatically convert date columns to instances of Carbon
   *
   */
  public function getDates()
  {
    return array('created_at', 'updated_at');
  }

  /**
   * Formats date if its a MongoDate.
   *
   * @param $value mixed - date attribute value
   * @return String
   */
  private function formatDate($value)
  {
    $date = $this->asDateTime($value);
    return $date->format('Y-m-d H:i:s');
  }

  /**
   * Accessor for created_at date. Formats to Y-m-d H:i:s.
   */
  public function getCreatedAtAttribute($value)
  {
    return $this->formatDate($value);
  }

  /**
   * Accessor for updated_at date. Formats to Y-m-d H:i:s.
   */
  public function getUpdatedAtAttribute($value)
  {
    return $this->formatDate($value);
  }

  /**
   * Define embedded relationship with the Campaign Model
   *
   */
  public function campaigns()
  {
    return $this->embedsMany('Northstar\Models\Campaign');
  }

  /**
   * Determines validation rules for user registration and authentication
   *
   * @param $data - User data to be validated
   * @param bool $auth - Whether validation should use authentication ruleset
   * @return bool - Success/failure of validation
   */
  public function validate($data, $auth = false)
  {
    $rules = ($auth == true) ? $this->auth_rules : $this->rules;

    $v = Validator::make($data, $rules);

    $v->sometimes('email', 'required', function ($data) {
      $mobile = (empty($data->mobile)) ? true : false;
      return $mobile;
    });

    $v->sometimes('mobile', 'required', function ($data) {
      $email = (empty($data->email)) ? true : false;
      return $email;
    });

    if ($v->fails()) {
      $this->messages = $v->messages()->all();
      return false;
    }

    return true;
  }

  /**
   * Generate a token to authenticate a user
   *
   * @return mixed
   */
  public function login()
  {
    $token = Token::getInstance();
    $token->user_id = $this->_id;
    $token->save();

    return $token;
  }

  /**
   * Get the currently authenticated user from the session token.
   *
   * @return User
   */
  public static function current()
  {
    $token = Request::header('Session');
    $user = Token::userFor($token);

    return $user;
  }

}
