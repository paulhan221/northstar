<?php namespace Northstar\Http\Controllers;

use Northstar\Services\AWS;
use Northstar\Models\User;
use Illuminate\Http\Request;

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
    if ($request->file('photo'))
    {
        $file = $request->file('photo');
        $isFile = true;
    } else {
        $file = $request->photo;
        $isFile = false;
    }

    $this->validate($request, [
      'photo' => 'required'
    ]);

    $filename = $this->aws->storeImage('profiles-dosomething-org', $id, $file, $isFile);

    // Save filename to User model
    $user = User::where($id)->first();
    $user->avatar = $filename;
    $user->save();

    // Respond to user with success and photo URL
    return $this->respond(['url' => $filename]);
  }
}

