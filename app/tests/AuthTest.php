<?php

class AuthTest extends TestCase {

  /**
   * Migrate database and set up HTTP headers
   *
   * @return void
   */
  public function setUp()
  {
    parent::setUp();
    Route::enableFilters();

    Artisan::call('migrate');
    $this->seed();

    $this->server = array(
      'CONTENT_TYPE' => 'application/json',
      'HTTP_X-DS-Application-Id' => '456',
      'HTTP_X-DS-REST-API-Key' => 'abc4324',
      'HTTP_Session' => 'S0FyZmlRNmVpMzVsSzJMNUFreEFWa3g0RHBMWlJRd0tiQmhSRUNxWXh6cz0='
    );
  }

  /**
   * Test for logging in a user
   * POST /login
   *
   * @return void
   */
  public function testLogin()
  {
    // User login info
    $credentials = array(
      'email' => 'test@dosomething.org',
      'password' => 'secret'
    );

    $response = $this->call('POST', 'v1/login', array(), array(), $this->server, json_encode($credentials));
    $content = $response->getContent();

    // The response should return a 200 Created status code
    $this->assertEquals(200, $response->getStatusCode());

    // Response should be valid JSON
    $this->assertJson($content);
  }

  /**
   * Test for logging out a user
   * POST /logout
   *
   * @return void
   */
  public function testLogout()
  {
    $response = $this->call('POST', 'v1/logout', array(), array(), $this->server);
    $content = $response->getContent();

    // The response should return a 200 Created status code
    $this->assertEquals(200, $response->getStatusCode());

    // Response should be valid JSON
    $this->assertJson($content);
  }
}
