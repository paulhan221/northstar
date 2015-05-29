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

  public function store(Request $request, $id)
  {
    $file = $request->file('photo');

    $this->validate($request, [
      'photo' => 'required|image|mimes:jpeg,jpg|max:8000'
    ]);

    $filename = $this->aws->storeImage('avatars', $id, $file);

    // Save filename to User model
    $user = User::where($id)->first();
    $user->avatar = $filename;
    $user->save();

    // Respond to user with success
    return $this->respond('Photo Uploaded!');
  }
}

