<?php

use Northstar\Services\DrupalAPI;

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
    $check = Input::only('email', 'mobile');
    $input = Input::all();

    $user = false;

    // Does this user exist already?
    if (Input::has('email')) {
      $user = User::where('email', '=', $check['email'])->first();
    } elseif (Input::has('mobile')) {
      $user = User::where('mobile', '=', $check['mobile'])->first();
    }

    // If there is no user found, create a new one.
    if (!$user) {
      $user = new User;

      // This validation might not be needed, the only validation happening right now
      // is for unique email or phone numbers, and that should return a user
      // from the query above.
      if ($user->validate($input)) {
        $user->validate($input);
      } else {
        return Response::json($user->messages(), 401);
      }
    }
    // Update or create the user from all the input.
    try {
      foreach($input as $key => $value) {
        if ($key == 'interests'){
          // Remove spaces, split on commas.
          $interests = array_map('trim', explode(',', $value));
          $user->push('interests', $interests, true);
        } elseif (!empty($value)) {
          $user->$key = $value;
        }
      }
      // Do we need to forward this user to drupal?
      // If query string exists, make a drupal user.
      if (Input::has('create_drupal_user') && !$user->drupal_id) {
        try {
          $drupal = new DrupalAPI;
          $drupal_id = $drupal->register($user);
          $user->drupal_id = $drupal_id;
        } catch (Exception $e) {
          // @TODO: figure out what to do if a user isn't created.
          // This could be a failure for so many reasons
          // User is already registered/email taken
          // Or just a general failure - do we try again?
        }
      }

      $user->save();

      // Log the user in & attach their session token to response.
      $token = $user->login();
      $user->session_token = $token->key;

      return $user;
    }
    catch(\Exception $e) {
      return Response::json($e, 401);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param $term - string
   *   term to search by (eg. mobile, drupal_id, id, email, etc)
   * @param $id - string
   *  the actual value to search for
   *
   * @return Response
   */
  public function show($term, $id)
  {
    $user = '';

    // Type cast id fields as ints.
    if (strpos($term,'_id') !== false && $term !== '_id') {
      $id = (int) $id;
    }

    // Find the user.
    $user = User::where($term, $id)->get();
    if(!$user->isEmpty()) {
      return Response::json($user, 200);
    }
    return Response::json('The resource does not exist', 404);

  }


  /**
   * Update the specified resource in storage.
   * PUT /users
   *
   * @param $id - User ID
   * @return Response
   */
  public function update($id)
  {
    $input = Input::all();

    $user = User::where('_id', $id)->first();

    if($user instanceof User) {
      foreach($input as $key => $value) {
        if ($key == 'interests'){
          $interests = array_map('trim', explode(',', $value));
          $user->push('interests', $interests, true);
        }
        // Only update attribute if value is non-null.
        elseif(isset($key) && !is_null($value)) {
          $user->$key = $value;
        }
      }

      $user->save();

      $response = array('updated_at' => $user->updated_at);

      return Response::json($response, 202);
    }

    return Response::json("The resource does not exist", 404);
  }

  /**
   * Delete a user resource.
   * DELETE /users/:id
   *
   * @param $id - User ID
   * @return Response
   */
  public function destroy($id)
  {
    $user = User::where('_id', $id)->first();

    if ($user instanceof User) {
      $user->delete();

      return Response::json("No Content", 204);
    }

    return Response::json("The resource does not exist", 404);
  }

}
