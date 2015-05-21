<?php namespace Northstar\Http\Controllers;

use Northstar\Models\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SignupGroupController extends Controller
{

    /**
     * Display the users who share the specified signup group id.
     * GET /signup-group/:id
     *
     * @param int $id - Signup Group ID
     *
     * @return \Illuminate\Http\Response
     * @throws NotFoundHttpException
     */
    public function show($id)
    {
        // signup_id is saved as a number and signup_source is saved as a string
        $group = User::where('campaigns', 'elemMatch', ['signup_id' => (int)$id])
                        ->orWhere('campaigns', 'elemMatch', ['signup_source' => $id])->get();

        if (count($group) == 0) {
            throw new NotFoundHttpException("No users found for the group ID.");
        }
        else {
            return $this->respond($group);
        }
    }

}