<?php

class CampaignController extends \BaseController {

  /**
   * Returns a user's campaigns
   *
   * @param $term - string
   *   term to search by (eg. mobile, drupal_id, id, etc)
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
    $user = User::where($term, $id)->first();
    if($user instanceof User) {
      $campaigns = $user->campaigns;
      return Response::json($campaigns, 200);
    }
    return Response::json('The resource does not exist', 404);
  }


  /**
   * Store a newly created campaign signup in storage.
   * POST /campaigns/:nid/signup
   *
   * @return Response
  */
  public function signup($id)
  {
    $input = Input::only('sid');
    $campaign = new Campaign;
    if (!$campaign->validate($input)) {
      return Response::json($campaign->getValidationMessages(), 401);
    }
    else {
      $sid = Input::get('sid');
      if (!$sid) {
        return Response::json("Campaign SID not provided", 401);
      }
    }

    $nid = (int) $id;
    if (!$nid) {
      return Response::json("Campaign node ID not provided", 401);
    }

    $token = Request::header('Session');
    $user = Token::userFor($token);
    $campaign = $user->campaigns()->where('nid', '=', $nid)->first();

    if ($campaign instanceof Campaign) {
      return Response::json("Campaign already exists", 401);
    }

    $campaign = new Campaign;
    $campaign->nid = $nid;
    $campaign->sid = $sid;
    $campaign = $user->campaigns()->save($campaign);
    $response = array(
      'created_at' => $campaign->created_at,
      'sid' => $campaign->sid
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
