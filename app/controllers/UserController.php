<?php

class UserController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /users
	 *
	 * @return Response
	 */
	public function getUsers()
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
			return Response::json($user, 200);
		}
	}


	/**
	 * Store a newly created resource in storage.
	 * POST /users
	 *
	 * @return Response
	 */
	public function postUsers()
	{
		$input = Input::json()->all();
		$validator = Validator::make($input, User::$rules);

		if($validator->passes()) {

			try {
				$user = new User;
				foreach($input as $key => $value) {
					if($key == 'password') {
						$user->$key = Hash::make($value);
					}
					elseif($key = 'email') {
						$user->$key = mb_strtolower($value);
					}
					elseif(isset($key)) {
						$user->$key = $value;
					}
				}
				
				$user->save();

				$response = array(
					'created_at' => $user->created_at->format('Y-m-d H:i:s'), 
					'_id' => $user->_id
					);

				return Response::json($response, 201);

				}
			catch(\Exception $e) {
				return Response::json($e, 401);
			}
			
		}
		else {
			return Response::json($validator->messages()->all(), 401);
		}

	}


	/**
	 * Update the specified resource in storage.
	 * PUT /users
	 *
	 * @return Response
	 */
	public function putUsers()
	{	
		$input = Input::json()->all();
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
			return Response::json("The resource does not exist", 404);
		}
		else {
			foreach($input as $key => $value) {
				if($key == 'password') {
					$user->$key = Hash::make($value);
				}
				elseif($key = 'email') {
					$user->$key = mb_strtolower($value);
				}
				elseif(isset($key)) {
					$user->$key = $value;
				}
			}
			$user->save();

			$response = array('updated_at' => $user->updated_at->format('Y-m-d H:i:s'));

			return Response::json($response, 202);
		}
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
