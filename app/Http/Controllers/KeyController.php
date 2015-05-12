<?php namespace Northstar\Http\Controllers;

use Northstar\Models\ApiKey;
use Input;
use Response;

class KeyController extends Controller
{

  /**
   * Display a listing of the resource.
   * GET /keys
   *
   * @return Response
   */
  public function index()
  {
    $keys = ApiKey::all();
    return Response::json($keys, 200);
  }


  /**
   * Store a newly created resource in storage.
   * POST /keys
   *
   * @return Response
   */
  public function store()
  {
    // Get the app name from submission.
    if (Input::has('app_name')) {
      $app_name = Input::get('app_name');
      $key = new ApiKey();
      $key->app_id = snake_case(str_replace(' ', '', $app_name));
      $key->api_key = str_random(40);
      // Save new key.
      $key->save();

      return Response::json($key, 200);
    }
    return Response::json('Missing required information', 400);
  }

  /**
   * Display the specified resource.
   *
   * @return Response
   */
  public function show($id)
  {
    // Find the user.
    $key = Key::where('id', $id)->get();
    if (!$key->isEmpty()) {
      return Response::json($key, 200);
    }
    return Response::json('The resource does not exist', 404);

  }


  /**
   * Delete a api key resource.
   * DELETE /key/:id
   *
   * @return Response
   */
  public function destroy($id)
  {

  }

}
