<?php namespace Northstar\Http\Controllers;

use Northstar\Models\User;
use Northstar\Models\Token;
use Illuminate\Http\Request;
use Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthController extends Controller
{

    /**
     * Authenticate a registered user
     *
     * @param Request $request
     * @return Response
     * @throws HttpException
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
            throw new NotFoundHttpException('Invalid username or password.');
        } elseif (Hash::check($input['password'], $user->password)) {
            $token = $user->login();
            $token->user = $user->toArray();

            // Return the session token with the user.
            $user->session_token = $token->key;
            $data = $user;
            return $this->respond($user);
        } else {
            throw new HttpException(412, 'Incorrect password.');
        }

    }

    /**
     * Logout the current user by invalidating their session token.
     * @return Response
     * @throws HttpException
     */
    public function logout(Request $request)
    {
        if (!$request->header('Session')) {
            throw new HttpException(422, 'No token given.');
        }

        $input_token = $request->header('Session');
        $token = Token::where('key', '=', $input_token)->first();
        $user = Token::userFor($input_token);

        if (empty($token)) {
            throw new NotFoundHttpException('No active session found.');
        } elseif ($token->user_id !== $user->_id) {
            throw new HttpException(403, 'You do not own this token.');
        } elseif ($token->delete()) {
            // Remove Parse installation ID. Disables push notifications.
            if ($request->has('parse_installation_ids')) {
                $removeId = $request->parse_installation_ids;
                $user->pull('parse_installation_ids', $removeId);
                $user->save();
            }

            return $this->respond('User logged out successfully.');
        } else {
            throw new HttpException(400, 'User could not log out. Please try again.');
        }

    }

}
