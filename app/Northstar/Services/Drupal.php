<?php namespace Northstar\Services\Drupal;

class DrupalAPI {

  protected $client;

  public function __construct()
  {
    $base_url = \Config::get('services.drupal.url');
    // Ideally this will connect to your local vagrant box.
    // if (\App::environment('local')) {
      // $base_url .=  ":" . \Config::get('services.drupal.port');
    // }
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
    // Get all campaigns
    if (!$id) {
      $response = $this->client->get('campaigns.json');
    }
    return $response->json();
  }
}