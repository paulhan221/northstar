<?php namespace Northstar\Http\Controllers;

use Northstar\Models\User;
use Northstar\Services\DrupalAPI;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SignupGroupController extends Controller
{
    protected $drupal;

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
        $group = User::where('campaigns', 'elemMatch', ['signup_id' => $id])
                        ->orWhere('campaigns', 'elemMatch', ['signup_group' => $id])->get();

        if (count($group) == 0) {
            throw new NotFoundHttpException("No users found for the group ID.");
        }

        // Get the campaign id associated with the signup group ID
        for ($i = 0; $i < count($group[0]->campaigns); $i++) {
            $campaign = $group[0]->campaigns[$i];
            if ($campaign->signup_id == $id || $campaign->signup_group == $id) {
                $campaign_id = $campaign->drupal_id;
                break;
            }
        }

        // Drupal API client
        $drupal = new DrupalAPI;

        foreach ($group as &$user) {
            if (isset($user->campaigns)) {
                for ($i = 0; $i < count($user->campaigns); $i++) {
                    // Cull campaigns that aren't associated with this group
                    if ($user->campaigns[$i]->drupal_id != $campaign_id) {
                        unset($user->campaigns[$i]);
                        continue;
                    }

                    if (isset($user->campaigns[$i]->reportback_id)) {
                        // get reportback data from drupal
                        $rbResponse = $drupal->reportbackContent($user->campaigns[$i]->reportback_id);
                        $user->campaigns[$i]->reportback_data = $rbResponse['data'];
                    }
                }
            }
        }

        $response = [
            'campaign_id' => $campaign_id,
            'users' => $group
        ];

        return $this->respond($response);
    }

}