<?php namespace Northstar\Http\Controllers;

use Northstar\Services\AWS;
use Northstar\Models\User;
use Illuminate\Http\Request;
use Validator;
use Input;
use Response;

class AvatarController extends Controller
{

  public function __construct(AWS $aws)
  {
      $this->aws = $aws;
  }

/**
 * Store a new avatar for a user.
 * POST northstar.com/users/{id}/avatar
 */
  public function store(User $user, Request $request)
  {
    // dd($request->all());

    // dd(Input::only('photo'));

    $file = $request->file('photo');

    $v = Validator::make(
      $request->all(),
      ['photo' => 'required|image|mimes:jpeg,jpg|max:8000']
    );

    if($v->fails())
      return Response::json(['error' => $v->errors()]);

    $filename = $this->aws->storeImage('avatars', $file);

    // Save filename to User model
    $user->avatar = $filename;
    $user->save();

    // Respond to user with success
    return response()->json('Great!', 200);
  }
}