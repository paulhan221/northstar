<?php namespace Northstar\Http\Controllers;

use Northstar\Models\User;
use Northstar\Models\Token;
use Input;
use Hash;
use Response;
use Request;

class AuthController extends BaseController {

  /**
   * Authenticate a registered user
   *
   * @return Response
   */
  public function login()
  {
    $input = Input::only('email', 'mobile', 'password');
    $user = new User;
    if($user->validate($input, true)) {
      if (Input::has('email')) {
        $user = User::where('email', '=', $input['email'])->first();
      }
      elseif (Input::has('mobile')) {
        $user = User::where('mobile', '=', $input['mobile'])->first();
      }
      if(!($user instanceof User)) {
        return Response::json("User is not registered.");
      }

      if(Hash::check($input['password'] , $user->password)) {
        $token = $user->login();
        $token->user = $user->toArray();

        // Return the session token with the user.
        $user->session_token = $token->key;
        return Response::json($user, 200);
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
   * Logout the current user by invalidating their session token.
   * @return Response
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
