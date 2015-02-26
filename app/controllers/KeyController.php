<?php


class KeyController extends \BaseController {

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
    if(!$key->isEmpty()) {
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