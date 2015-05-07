<?php namespace Northstar\Services\Drupal;

use GuzzleHttp\Client;
use Config;
use Cache;

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

  /**
   * Returns a token for making authenticated requests to the Drupal API.
   *
   * @return Array - Cookie & token for authenticated requests
   */
  private function authenticate()
  {
    $authentication = Cache::remember('drupal.authentication', 30, function() {
      $payload = [
        'username' => getenv('DRUPAL_API_USERNAME'),
        'password' => getenv('DRUPAL_API_PASSWORD')
      ];

      $response = $this->client->post('auth/login', [
        'body' => json_encode($payload)
      ]);

      $body = $response->json();

      $session_name = $body['session_name'];
      $session_value = $body['sessid'];

      return [
        'cookie' => [$session_name => $session_value],
        'token' => $body['token']
      ];
    });

    return $authentication;
  }

  /**
   * Get the CSRF token for the authenticated API session.
   *
   * @return String - token
   */
  private function getAuthenticationToken()
  {
    return $this->authenticate()['token'];
  }

  /**
   * Get the cookie for the authenticated API session.
   *
   * @return Array - cookie key/value
   */
  private function getAuthenticationCookie()
  {
    return $this->authenticate()['cookie'];
  }

  /**
   * Get list of campaigns, or individual campaign information.
   * @see https://github.com/DoSomething/dosomething/wiki/API#campaigns
   *
   * @param int $id - Optional campaign ID to get information on.
   * @return mixed
   */
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
   * Forward registration to Drupal.
   * @see: https://github.com/DoSomething/dosomething/wiki/API#create-a-user
   *
   * @param \User $user - User to be registered on Drupal site
   *
   * @return int - Created Drupal user UID
   */
  public function register($user)
  {
    $payload = $user->toArray();

    // Format user object for consumption by Drupal API.
    $payload['birthdate'] = date('Y-m-d', strtotime($user->birthdate));
    $payload['user_registration_source'] = $user->source;

    $response = $this->client->post('users', [
      'body' => json_encode($payload),
    ]);

    return $response->uid;
  }

  /**
   * Create a new campaign signup on the Drupal site.
   * @see: https://github.com/DoSomething/dosomething/wiki/API#campaign-signup
   *
   * @param String $user_id - UID of user on the Drupal site
   * @param String $campaign_id - NID of campaign on the Drupal site
   * @param String $source - Sign up source (e.g. web, iPhone, etc.)
   *
   * @return String - Signup ID
   * @throws \Exception
   */
  public function campaignSignup($user_id, $campaign_id, $source)
  {
    $payload = [
      'uid' => $user_id,
      'source' => $source
    ];

    $response = $this->client->post('campaigns/' . $campaign_id . '/signup', [
      'body' => json_encode($payload),
      'cookies' => $this->getAuthenticationCookie(),
      'headers' => [
        'X-CSRF-Token' => $this->getAuthenticationToken()
      ]
    ]);

    $body = $response->json();
    $signup_id = $body[0];

    if(!$signup_id) {
      // @TODO: Drupal API returns false if signup already exists. What is a graceful way of handling this?
      throw new \Exception('Could not create signup.');
    }

    return $signup_id;
  }
}