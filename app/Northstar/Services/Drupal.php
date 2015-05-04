<?php namespace Northstar\Services\Drupal;

use GuzzleHttp\Client;
use Config;

class DrupalAPI {

  protected $client;

  public function __construct()
  {
    $base_url = Config::get('services.drupal.url');
    $version = Config::get('services.drupal.version');

    $this->client = new Client([
      'base_url' => [$base_url . '/api/{version}/', ['version' => $version]],
      'defaults' => [
        'headers' => [
          'Content-Type' => 'application/json',
          'Accept' => 'application/json'
        ]
      ],
    ]);
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
  public function register($d_user)
  {
    try {
      $d_user->birthdate = date('Y-m-d', strtotime($user->birthdate));
      $d_user->user_registration_source = $user->source;
      $response = $this->client->post('users', [
        'body' => json_encode($user),
        ]);
      return $response->json();
    } catch (\Exception $e) {
      // whatever.
      return;
    }
  }

  /**
   * Create a new campaign signup on the Drupal site.
   * @param $drupal_id    String - UID of user on the Drupal site
   * @param $campaign_id  String - NID of campaign on the Drupal site
   * @param $source       String - Sign up source (e.g. web, iPhone, etc.)
   *
   * @return String - Signup ID
   */
  public function campaignSignup($drupal_id, $campaign_id, $source)
  {
    $payload = [
      'uid' => $drupal_id,
      'source' => $source
    ];

    $response = $this->client->post('campaigns/' . $campaign_id . '/signup', [
      'body' => json_encode($payload)
    ]);

    dd($response);

    return $response->sid;
  }
}