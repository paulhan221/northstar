<?php namespace Northstar\Http\Controllers;

use Illuminate\Http\Request;
use Northstar\Services\DrupalAPI;
use Northstar\Models\User;


class KudosController extends Controller
{

    public function __construct(DrupalAPI $drupal)
    {
        $this->drupal = $drupal;
    }

   /**
   * Store a new kudos from a user.
   * Kudos request made from mobile app and forwarded to Northstar.
   * Northstar finds the drupal user and sends request on to Drupal.
   * POST /kudos
   *
   * @param Request $request
   * @return Response
   */
    public function store(Request $request)
    {
        $user = User::current();

        $drupal_id = $user->drupal_id;

        $response = $this->drupal->storeKudos($drupal_id, $request);

        // Fire kudo event.
        event(new UserGotKudo($user));

        return $this->respond($response);
    }

}

