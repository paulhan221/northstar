<?php namespace Northstar\Services\Drupal;

class DrupalAPI {

  protected $client;

  public function __construct()
  {
    $base_url = \Config::get('services.drupal.url');
    if (\App::environment('local')) {
      $base_url .=  ":" . \Config::get('services.drupal.port');
    }
    $base_url .= '/api';
    $version = \Config::get('services.drupal.version');
    $client = new \GuzzleHttp\Client([
      'base_url' => [$base_url . '/{version}/', ['version' => $version]],
      'defaults' => array(
        'headers' => [
          'Content-Type' => 'application/json',
          'Accept' => 'application/json'
          ]
        ),
    ]);
    $this->client = $client;
  }

  public function campaigns($id = NULL)
  {
    // Get all campaigns if there's no id set.
    if (!$id) {
      $response = $this->client->get('campaigns.json');
    }
    else {
      $response = $this->client->get('content/' . $id . '.json');
    }
    return $response->json();
  }

  /**
   * Forward registration to drupal.
   */
  public function register($user)
  {
    $user->birthdate = date('Y-m-d', strtotime($user->birthdate));
    $user->user_registration_source = $user->source;
    $response = $this->client->post('users', [
      'body' => json_encode($user),
      ]);
    return $response->json();

  }
}