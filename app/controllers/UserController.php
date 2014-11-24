<?php

class UserController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /users
	 *
	 * @return Response
	 */
	public function index()
	{
		$drupal_uid = Input::has('drupal_uid') ? (int) Input::get('drupal_uid') : false;
		$doc_id = Input::has('doc_id') ? (int) Input::get('drupal_uid') : false;
		$mobile = Input::has('mobile') ? Input::get('drupal_uid') : false;
		$email = Input::has('email') ? Input::get('drupal_uid') : false;

		if($drupal_uid) {
			$user = User::where('drupal_uid', $drupal_uid)->first();
		}
		elseif($doc_id) {
			$user = User::where('doc_id', $doc_id)->first();
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
			return Response::json("The resource does not exist", 404);
		}
		else {
			return Response::json($user, 200);
		}
	}


	/**
	 * Store a newly created resource in storage.
	 * POST /users
	 *
	 * @return Response
	 */
	public function store()
	{
		$user = '';
		$validator = Validator::make(Input::all(), User::$rules);

		if($validator->passes()) {

			try {
				$user = new User;
			    $user->email = mb_strtolower(Input::get('email'));
			    $user->mobile = Input::get('mobile');
			    $user->password = Hash::make(Input::get('password'));
			    $user->birthdate = Input::get('birthdate');
			    $user->first_name = Input::get('first_name');
			    $user->drupal_uid = uniqid();
			    $user->doc_id = uniqid();
			    $user->save();

			    return Response::json([
			    	'created_at' => $user->created_at, 
			    	'doc_id' => $user->doc_id
			    	], 
			        201
			    );
			}
			catch(\Exception $e) {
				return Response::json($e, 401);
			}
			
		}
		else {
			return Response::json('Validation did not pass', 401);
		}

	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 * PUT /users
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Logging In
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function login()
	{
		//
	}


}
