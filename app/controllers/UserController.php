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
    //@TODO: set sensible limit here.
    $limit = Input::get('limit') ?: 20;
    $users = User::paginate($limit);
    return Response::json($users, 200);
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
        //@TODO: is there a better way to get this to the mutator?
        Session::flash('country', $input['country']);
        foreach($input as $key => $value) {
          if(isset($key)) {
            $user->$key = $value;
          }
        }

        $user->save();

        $response = array(
          USER_RESPONSE::created_at => $user->created_at,
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
   * Display the specified resource.
   *
   * @param $term - string
   *   term to search by (eg. mobile, drupal_id, id, etc)
   * @param $id - string
   *  the actual value to search for
   *
   * @return Response
   */
  public function show($term, $id)
  {
    $user = '';
    // Find the user.
    $user = User::where($term, $id)->get();
    if(is_object($user)) {
      return Response::json($user, 200);
    }
    return Response::json('The resource does not exist', 404);

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

      $response = array(USER_RESPONSE::updated_at => $user->updated_at);

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
          USER_RESPONSE::created_at => $user->created_at,
          USER_RESPONSE::updated_at => $user->updated_at,
          USER_PARAMS::_id => $user->_id,
          USER_RESPONSE::session_token => $token->key
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
  const address_street1 = 'addr_street1';
  const address_street2 = 'addr_street2';
  const address_city = 'addr_city';
  const address_state = 'addr_state';
  const address_zip = 'addr_zip';
  const country = 'country';
  const birthdate = 'birthdate';
  const first_name = 'first_name';
  const last_name = 'last_name';
  // Sources
  const drupal_id = 'drupal_id';
  const cgg_id = 'cgg_id';

  public static function editableKeys() {
    return array(
        self::email,
        self::mobile,
        self::password,
        self::drupal_id,
        self::cgg_id,
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

/**
 * Abstract class defining string values for response properties.
 */
abstract class USER_RESPONSE {
  const session_token = 'session_token';
  const created_at = 'created_at';
  const updated_at = 'updated_at';
}