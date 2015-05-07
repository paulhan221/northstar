<?php

use Northstar\Services\Drupal\DrupalAPI;

class CampaignController extends \BaseController {

  /**
   * Drupal API wrapper.
   * @var DrupalAPI
   */
  protected $drupal;

  public function __construct()
  {
    $this->drupal = new DrupalAPI;
  }

  /**
   * Returns a user's campaigns
   * GET /users/:term/:id/campaigns
   *
   * @param $term string - Term to search by (eg. mobile, drupal_id, id, etc)
   * @param $id   string - The value to search for
   *
   * @return Response
   */
  public function show($term, $id)
  {
    // Type cast id fields as ints.
    if (strpos($term,'_id') !== false && $term !== '_id') {
      $id = (int) $id;
    }

    // Find the user.
    $user = User::where($term, $id)->first();
    if($user instanceof User) {
      $campaigns = $user->campaigns;
      return Response::json($campaigns, 200);
    }
    return Response::json('The resource does not exist', 404);
  }


  /**
   * Sign user up for a given campaign.
   * POST /campaigns/:campaign_id/signup
   *
   * @param $campaign_id - Drupal campaign node ID
   * @return Response
   */
  public function signup($campaign_id)
  {
    // Build request object
    $request = Input::all();
    $request['campaign_id'] = $campaign_id;

    // Validate request
    $validator = Validator::make($request, [
      'campaign_id' => ['required', 'integer'],
      'source' => ['required']
    ]);

    if($validator->fails()) {
      return Response::json($validator->messages(), 401);
    }

    // Get the currently authenticated Northstar user.
    $user = User::current();

    // Check if campaign signup already exists.
    $campaign = $user->campaigns()->where('drupal_id', $campaign_id)->first();

    if ($campaign) {
      return Response::json("Campaign signup already exists", 401);
    }

    // Return an error if the user doesn't exist.
    if(!$user->drupal_id) {
      return Response::json('The user must have a Drupal ID to sign up for a campaign.', 401);
    }

    // Create a Drupal signup via Drupal API, and store SID in Northstar.
    $signup_id = $this->drupal->campaignSignup($user->drupal_id, $campaign_id, Input::get('source'));

    // Save reference to the signup on the user object.
    $campaign = new Campaign;
    $campaign->drupal_id = $campaign_id;
    $campaign->signup_id = $signup_id;
    $campaign = $user->campaigns()->save($campaign);

    $response = array(
      'signup_id' => $campaign->signup_id,
      'created_at' => $campaign->created_at,
    );

    return Response::json($response, 201);
  }


  /**
   * Store a newly created campaign report back in storage.
   * POST /campaigns/:nid/reportback
   *
   * @return Response
  */
  public function reportback($id)
  {
    return Response::json('Not yet implemented.', 501);

    // ...
  }

  /**
   * Update a campaign report back in storage.
   * PUT /campaigns/:nid/reportback
   *
   * @return Response
   */
  public function updateReportback($id)
  {
    return Response::json('Not yet implemented.', 501);

    // ...
  }

}
