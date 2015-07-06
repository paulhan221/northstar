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
        // signup_id and signup_group are saved as numbers
        $group = User::where('campaigns', 'elemMatch', ['signup_id' => (int)$id])
                        ->orWhere('campaigns', 'elemMatch', ['signup_group' => (int)$id])->get();

        if (count($group) == 0) {
            throw new NotFoundHttpException("No users found for the group ID.");
        } else {
            // Get the campaign id associated with the signup group ID
            for ($i = 0; $i < count($group[0]->campaigns); $i++) {
                $campaign = $group[0]->campaigns[$i];
                if ($campaign->signup_id == $id || $campaign->signup_group == $id) {
                    $campaign_id = $campaign->drupal_id;
                    break;
                }
            }

            $response = [
                'campaign_id' => $campaign_id,
                'users' => $group
            ];

            return $this->respond($response);
        }
    }

}