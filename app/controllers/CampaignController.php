<?php

class CampaignController extends \BaseController {

  /**
   * Display a listing of the resource.
   * GET users/campaigns
   *
   * @return Response
  */
  public function index()
  {

  }

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
    $input = Input::only(SIGNUP_ATTRIBUTE::sid);
    $campaign = new Campaign;
    if (!$campaign->validate($input)) {
      return Response::json($campaign->getValidationMessages(), 401);
    }
    else {
      $sid = Input::get(SIGNUP_ATTRIBUTE::sid);
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
      CAMPAIGN_RESPONSE::created_at => $campaign->created_at,
      SIGNUP_ATTRIBUTE::sid => $campaign->sid
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
    $input = Input::only(REPORTBACK_ATTRIBUTE::editableKeys());
    $campaign = new Campaign;
    if (!$campaign->validate($input)) {
      return Response::json($campaign->getValidationMessages(), 401);
    }

    $rbid = Input::get(REPORTBACK_ATTRIBUTE::rbid);
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

      $response = array(
        CAMPAIGN_RESPONSE::created_at => $campaign->created_at,
      );

      $statusCode = 201;
    }
    else {
      // Only input non-null values
      $input = array_filter($input, function($val) { return !is_null($val); });
      $campaign->fill($input);
      $campaign = $user->campaigns()->save($campaign);

      $response = array(
        CAMPAIGN_RESPONSE::updated_at => $campaign->updated_at,
      );
    }

    $response[REPORTBACK_ATTRIBUTE::rbid] = $campaign->rbid;

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
    $rbid = Input::get(REPORTBACK_ATTRIBUTE::rbid);
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
      ->where(REPORTBACK_ATTRIBUTE::rbid, '=', $rbid)
      ->first();

    if (!($campaign instanceof Campaign)) {
      return Response::json("Campaign does not exist", 401);
    }

    $input = Input::only(REPORTBACK_ATTRIBUTE::editableKeys());
    $input = array_filter($input, function($val) { return !is_null($val); });
    $campaign->fill($input);

    $campaign = $user->campaigns()->save($campaign);

    $response = array(
      CAMPAIGN_RESPONSE::updated_at => $campaign->updated_at,
      REPORTBACK_ATTRIBUTE::rbid => $campaign->rbid
    );

    return Response::json($response, 200);
  }

}

/**
 * Abstract class defining string values for response properties.
 */
abstract class CAMPAIGN_RESPONSE {
  const created_at = 'created_at';
  const updated_at = 'updated_at';
}