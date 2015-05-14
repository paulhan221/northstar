<?php namespace Northstar\Http\Controllers;

use Northstar\Models\User;
use Northstar\Models\Token;
use Illuminate\Http\Request;
use Hash;

class AuthController extends Controller
{

    /**
     * Authenticate a registered user
     *
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        $input = $request->only('email', 'mobile', 'password');

        $this->validate($request, [
            'email' => 'email',
            'password' => 'required'
        ]);

        if ($request->has('email')) {
            $user = User::where('email', '=', $input['email'])->first();
        } elseif ($request->has('mobile')) {
            $user = User::where('mobile', '=', $input['mobile'])->first();
        }
        if (!($user instanceof User)) {
            return response()->json("User is not registered.");
        }

        if (Hash::check($input['password'], $user->password)) {
            $token = $user->login();
            $token->user = $user->toArray();

            // Return the session token with the user.
            $user->session_token = $token->key;
            return response()->json($user, 200);
        } else {
            return response()->json("Incorrect password.", 412);
        }

    }

    /**
     * Logout the current user by invalidating their session token.
     * @return Response
     */
    public function logout(Request $request)
    {
        if (!$request->header('Session')) {
            return response()->json('No token given.');
        }

        $input_token = $request->header('Session');
        $token = Token::where('key', '=', $input_token)->first();
        $user = Token::userFor($input_token);

        if (empty($token)) {
            return response()->json('No active session found.');
        }
        if ($token->user_id !== $user->_id) {
            response()->json('You do not own this token.');
        }
        if ($token->delete()) {
            return response()->json('User logged out successfully.', 200);
        } else {
            return response()->json('User could not log out. Please try again.');
        }

    }

}
