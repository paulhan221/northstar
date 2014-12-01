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
		$drupal_uid = Input::has('drupal_uid') ? (int) Input::get('drupal_uid') : false;
		$id = Input::has('_id') ? Input::get('_id') : false;
		$mobile = Input::has('mobile') ? Input::get('mobile') : false;
		$email = Input::has('email') ? Input::get('email') : false;

		if($drupal_uid) {
			$user = User::where('drupal_uid', $drupal_uid)->first();
		}
		elseif($id) {
			$user = User::where('_id', $id)->first();
		}
		elseif($mobile) {
			$user = User::where('mobile', $mobile)->first();
		}
		elseif($email) {
			$user = User::where('email', $email)->first();
		}
		else {
			$user = false;
		}

		if(!$user) {
			return Response::json('The resource does not exist', 404);
		}
		else {
			$campaigns = $user->campaigns;
			return Response::json($campaigns, 200);
		}
	}


	/**
	 * Store a newly created campaign signup in storage.
	 * POST /campaigns/:nid/signup
	 *
	 * @return Response
	 */
	public function signup($id)
	{
		if($id) {
			$campaign = new Campaign;
			$campaign->nid = $id;
			$campaign->sid = $id.'_'.rand(1,50);
			$user = User::first();
			$campaign = $user->campaigns()->save($campaign);
			$response = array(
				'created_at' => $campaign->created_at->format('Y-m-d H:i:s'),
				'sid' => $campaign->sid
			);
			return Response::json($response, 201);
		}
		else {
			return Response::json("Campaign node ID not provided", 401);
		}
	}

}
