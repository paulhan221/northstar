<?php

class UserController extends \BaseController {

  /**
   * Display a listing of the resource.
   * GET /users
   *
   * @return Response
  */
  public function index()
  {   
    $user = '';
    $drupal_uid = Input::has(USER_PARAMS::drupal_uid) ? (int) Input::get(USER_PARAMS::drupal_uid) : false;
    $id = Input::has(USER_PARAMS::_id) ? Input::get(USER_PARAMS::_id) : false;
    $mobile = Input::has(USER_PARAMS::mobile) ? Input::get(USER_PARAMS::mobile) : false;
    $email = Input::has(USER_PARAMS::email) ? Input::get(USER_PARAMS::email) : false;

    if($drupal_uid) {
      $user = User::where(USER_PARAMS::drupal_uid, $drupal_uid)->first();
    }
    elseif($id) {
      $user = User::where(USER_PARAMS::_id, $id)->first();
    }
    elseif($mobile) {
      $user = User::where(USER_PARAMS::mobile, $mobile)->first();
    }
    elseif($email) {
      $user = User::where(USER_PARAMS::email, $email)->first();
    }

    if($user instanceof User) {
      return Response::json($user, 200);
    }

    return Response::json('The resource does not exist', 404);
  }


  /**
   * Store a newly created resource in storage.
   * POST /users
   *
   * @return Response
   */
  public function store()
  {
    $input = Input::only(USER_PARAMS::editableKeys());

    $user = new User;

    if($user->validate($input)) {

      try {
        foreach($input as $key => $value) {
          if(isset($key)) {
            $user->$key = $value;
          }
        }
        
        $user->save();

        $response = array(
          RESPONSE_PARAMS::created_at => $user->created_at->format('Y-m-d H:i:s'),
          USER_PARAMS::_id => $user->_id
        );

        return Response::json($response, 201);
      }
      catch(\Exception $e) {
        return Response::json($e, 401);
      }
        
    }
    else {
      return Response::json($user->messages(), 401);
    }

  }


  /**
   * Update the specified resource in storage.
   * PUT /users
   *
   * @return Response
   */
  public function update($id)
  {
    $input = Input::only(USER_PARAMS::editableKeys());

    $user = User::where(USER_PARAMS::_id, $id)->first();

    if($user instanceof User) {
      foreach($input as $key => $value) {
        // Only update attribute if value is non-null.
        if(isset($key) && !is_null($value)) {
          $user->$key = $value;
        }
      }

      $user->save();

      $response = array(RESPONSE_PARAMS::updated_at => $user->updated_at->format('Y-m-d H:i:s'));

      return Response::json($response, 202);
    }

    return Response::json("The resource does not exist", 404);
  }

  /**
   * Delete a user resource.
   * DELETE /users/:id
   *
   * @return Response
   */
  public function destroy($id)
  {
    $user = User::where(USER_PARAMS::_id, $id)->first();

    if ($user instanceof User) {
      $user->delete();

      return Response::json("No Content", 204);
    }

    return Response::json("The resource does not exist", 404);
  }

  /**
   * Authenticate a registered user
   *
   * @return Response
   */
  public function login()
  {
    $input = Input::only(USER_PARAMS::email, USER_PARAMS::mobile, USER_PARAMS::password);
    $user = new User;
    
    if($user->validate($input, true)) {
      $user = User::where(USER_PARAMS::email, '=', Input::get(USER_PARAMS::email))->first();
      if(!($user instanceof User)) {
        $user = User::where(USER_PARAMS::mobile, '=', Input::get(USER_PARAMS::mobile))->first();
      }
      if(!($user instanceof User)) {
        return Response::json("User is not registered.");
      }
      
      if(Hash::check(Input::get(USER_PARAMS::password) , $user->password)) {
        $token = $user->login();
        $token->user = $user->toArray();

        $response = array(
          USER_PARAMS::email => $user->email,
          USER_PARAMS::mobile => $user->mobile,
          RESPONSE_PARAMS::created_at => $user->created_at->format('Y-m-d H:i:s'),
          RESPONSE_PARAMS::updated_at => $user->updated_at->format('Y-m-d H:i:s'),
          USER_PARAMS::_id => $user->_id,
          RESPONSE_PARAMS::session_token => $token->key
        );
        return Response::json($response, '200');
      }
      else {
        return Response::json("Incorrect password.", 412);
      }

    }
    else {
      return Response::json($user->messages(), 401);
    }

  }

  /**
   *  Logout a user: remove the specified active token from the database
   *  @param user User
   */
  public function logout() 
  {
    if (!Request::header('Session')) {
      return Response::json('No token given.');
    }

    $input_token = Request::header('Session');
    $token = Token::where('key', '=', $input_token)->first();
    $user = Token::userFor($input_token);

    if (empty($token)) {
      return Response::json('No active session found.');
    }
    if ($token->user_id !== $user->_id) {
      Response::json('You do not own this token.');
    }
    if ($token->delete()){
      return Response::json('User logged out successfully.', 200);
    }   
    else {
      return Response::json('User could not log out. Please try again.');
    }
          
  }

}

abstract class USER_PARAMS {
  // Params that cannot be modified by the user
  const _id = "_id";

  // Editable params
  const email = 'email';
  const mobile = 'mobile';
  const password = 'password';
  const drupal_uid = 'drupal_uid';
  const address_street1 = 'addr_street1';
  const address_street2 = 'addr_street2';
  const address_city = 'addr_city';
  const address_state = 'addr_state';
  const address_zip = 'addr_zip';
  const country = 'country';
  const birthdate = 'birthdate';
  const first_name = 'first_name';
  const last_name = 'last_name';

  public static function editableKeys() {
    return array(
        self::email,
        self::mobile,
        self::password,
        self::drupal_uid,
        self::address_street1,
        self::address_street2,
        self::address_city,
        self::address_state,
        self::address_zip,
        self::country,
        self::birthdate,
        self::first_name,
        self::last_name
    );
  }
};

abstract class RESPONSE_PARAMS {
  const session_token = 'session_token';
  const created_at = 'created_at';
  const updated_at = 'updated_at';
}