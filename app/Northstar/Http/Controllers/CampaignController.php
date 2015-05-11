<?php namespace Northstar\Http\Controllers;

use Northstar\Services\DrupalAPI;
use Northstar\Models\User;
use Northstar\Models\Campaign;
use Validator;
use Response;
use Input;

class CampaignController extends BaseController {

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

    // Return an error if the user doesn't exist.
    if(!$user->drupal_id) {
      return Response::json('The user must have a Drupal ID to sign up for a campaign.', 401);
    }

    // Check if campaign signup already exists.
    $campaign = $user->campaigns()->where('drupal_id', $campaign_id)->first();

    if ($campaign) {
      return Response::json("Campaign signup already exists", 401);
    }

    // Create a Drupal signup via Drupal API, and store signup ID in Northstar.
    $signup_id = $this->drupal->campaignSignup($user->drupal_id, $campaign_id, $request['source']);

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
   * POST /campaigns/:campaign_id/reportback
   *
   * @return Response
  */
  public function reportback($campaign_id)
  {
    // Build request object
    $request = Input::all();
    $request['campaign_id'] = $campaign_id;

    // Validate request
    $validator = Validator::make($request, [
      'campaign_id' => ['required', 'integer'],
      'quantity' => ['required', 'integer'],
      'why_participated' => ['required'],
      'file' => ['required', 'string'], // Data URL!
      'caption' => ['string']
    ]);

    if($validator->fails()) {
      return Response::json($validator->messages(), 401);
    }

    // Get the currently authenticated Northstar user.
    $user = User::current();

    // Return an error if the user doesn't exist.
    if(!$user->drupal_id) {
      return Response::json('The user must have a Drupal ID to submit a reportback.', 401);
    }

    // Check if campaign signup already exists.
    $campaign = $user->campaigns()->where('drupal_id', $campaign_id)->first();

    if (!$campaign) {
      return Response::json("User is not signed up for this campaign yet.", 401);
    }

    // Create a reportback via the Drupal API, and store reportback ID in Northstar
    $reportback_id = $this->drupal->campaignReportback($user->drupal_id, $campaign_id, [
      'quantity' => $request['quantity'],
      'why_participated' => $request['why_participated'],
      'file' => $request['file'],
      'caption' => $request['caption']
    ]);

    $campaign->reportback_id = $reportback_id;
    $campaign->save();

    return Response::json(['reportback_id' => $reportback_id, 'created_at' => $campaign->updated_at], 201);
  }

  /**
   * Update a campaign report back in storage.
   * PUT /campaigns/:campaign_id/reportback
   *
   * @return Response
   */
  public function updateReportback($campaign_id)
  {
    return Response::json('Not yet implemented.', 501);

    // ...
  }

}
