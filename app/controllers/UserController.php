<?php

class UserController extends \BaseController {

	/**
	 * Display a listing of the resource.
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
	 *
	 * @return Response
	 */
	public function store()
	{
		//
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
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
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
