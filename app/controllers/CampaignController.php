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
   * POST /campaigns/:id/signup
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
      'user' => ['sometimes', 'exists:users,_id'],
      'source' => ['required']
    ]);

    if($validator->fails()) {
      return Response::json($validator->messages(), 401);
    }

    // If given a Northstar user ID, get that user. Otherwise, use
    // the currently authenticated Northstar user.
    if($request['user']) {
      $user = User::find($request['user']);
    } else {
      $token = Request::header('Session');
      $user = Token::userFor($token);
    }

    // Check if campaign signup already exists.
    $campaign = $user->campaigns()->where('nid', $campaign_id)->first();

    if ($campaign) {
      return Response::json("Campaign signup already exists", 401);
    }

    // Return an error if the user doesn't exist.
    if(!$user->drupal_id) {
      return Response::json('The user must have a Drupal ID to sign up for a campaign.', 401);
    }

    // Create a Drupal signup via Drupal API, and store SID in Northstar.
    $sid = $this->drupal->campaignSignup($user->drupal_id, $campaign_id, Input::get('source'));

    // Save reference to the signup on the user object.
    $campaign = new Campaign;
    $campaign->nid = $campaign_id;
    $campaign->sid = $sid;
    $campaign = $user->campaigns()->save($campaign);

    $response = array(
      'sid' => $campaign->sid,
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
    $input = Input::only('rbid', 'file_url', 'quantity', 'why_participated');
    $campaign = new Campaign;
    if (!$campaign->validate($input)) {
      return Response::json($campaign->getValidationMessages(), 401);
    }

    $rbid = Input::get('rbid');
    if (!$rbid) {
      return Response::json("Campaign RBID not provided", 401);
    }

    $nid = (int) $id;
    if (!$nid) {
      return Response::json("Campaign node ID not provided", 401);
    }

    $statusCode = 200;
    $token = Request::header('Session');
    $user = Token::userFor($token);
    $campaign = $user->campaigns()->where('nid', '=', $nid)->first();

    if (!($campaign instanceof Campaign)) {
      $campaign = new Campaign;
      $campaign->nid = $nid;

      // Only input non-null values
      $input = array_filter($input, function($val) { return !is_null($val); });
      $campaign->fill($input);
      $campaign = $user->campaigns()->save($campaign);

      $response = [
        'created_at' => $campaign->created_at,
      ];

      $statusCode = 201;
    }
    else {
      // Only input non-null values
      $input = array_filter($input, function($val) { return !is_null($val); });
      $campaign->fill($input);
      $campaign = $user->campaigns()->save($campaign);

      $response = [
        'updated_at' => $campaign->updated_at,
      ];
    }

    $response['rbid'] = $campaign->rbid;

    return Response::json($response, $statusCode);
  }

  /**
   * Update a campaign report back in storage.
   * PUT /campaigns/:nid/reportback
   *
   * @return Response
   */
  public function updateReportback($id)
  {
    $rbid = Input::get('rbid');
    if (!$rbid) {
      return Response::json("Campaign RBID not provided", 401);
    }

    $nid = (int) $id;
    if (!$nid) {
      return Response::json("Campaign node ID not provided", 401);
    }

    $token = Request::header('Session');
    $user = Token::userFor($token);
    $campaign = $user->campaigns()
      ->where('nid', '=', $nid)
      ->where('rbid', '=', $rbid)
      ->first();

    if (!($campaign instanceof Campaign)) {
      return Response::json("Campaign does not exist", 401);
    }

    $input = Input::only('rbid', 'file_url', 'quantity', 'why_participated');
    $input = array_filter($input, function($val) { return !is_null($val); });
    $campaign->fill($input);

    $campaign = $user->campaigns()->save($campaign);

    $response = array(
      'updated_at' => $campaign->updated_at,
      'rbid' => $campaign->rbid
    );

    return Response::json($response, 200);
  }

}
