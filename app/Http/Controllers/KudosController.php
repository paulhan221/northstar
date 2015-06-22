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

    public function store(Request $request)
    {
        $user = User::current();

        $drupal_id = $user->drupal_id;

        $response = $this->drupal->storeKudos($drupal_id, $request);

        return $this->respond($response);
    }

}

