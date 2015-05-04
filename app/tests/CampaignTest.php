<?php

class CampaignTest extends TestCase {

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
   * Test for retrieving a user's campaigns
   * GET /users/campaigns
   *
   * @return void
   */
  public function testGetCampaignsFromUser()
  {
    $response = $this->call('GET', 'v1/users/email/test@dosomething.org/campaigns', array(), array(), $this->server);
    $content = $response->getContent();

    // The response should return a 200 OK status code
    $this->assertEquals(200, $response->getStatusCode());
    
    // Response should be valid JSON
    $this->assertJson($content);
  }


  /**
   * Test for submiting a campaign signup
   * POST /campaigns/:nid/signup
   *
   * @return void
   */
  public function testSubmitCampaignSignup()
  {   
    $payload = [
      'user' => '5480c950bffebc651c8b456f',  // Test user ID
      'source' => 'test'
    ];

    $response = $this->call('POST', 'v1/campaigns/123/signup', [], [], $this->server, json_encode($payload));
    $content = $response->getContent();
    $data = json_decode($content, true);

    // The response should return a 201 Created status code
    $this->assertEquals(201, $response->getStatusCode());

    // Response should be valid JSON
    $this->assertJson($content);

    // Response should return created at and sid columns
    $this->assertArrayHasKey('created_at', $data);
    $this->assertArrayHasKey('sid', $data);
  }

  /**
   * Test for submiting a campaign report back.
   * POST /campaigns/:nid/reportback
   *
   * @return void
   */
  public function testSubmitCampaignReportback()
  {   
    // Campaign reportback data
    $rb = array(
      'rbid' => 100,
      'quantity' => 10,
      'why_participated' => 'I love helping others',
      'file_url' => 'http://example.test/example.png'
    );

    $response = $this->call('POST', 'v1/campaigns/123/reportback', array(), array(), $this->server, json_encode($rb));
    $content = $response->getContent();
    $data = json_decode($content, true);

    // The response should return a 201 Created status code
    $this->assertEquals(201, $response->getStatusCode());

    // Response should be valid JSON
    $this->assertJson($content);

    // Response should return created at and rbid columns
    $this->assertArrayHasKey('created_at', $data);
    $this->assertArrayHasKey('rbid', $data);
  }

  /**
   * Test for successful update of a campaign report back.
   * PUT /campaigns/:nid/reportback
   *
   * @return void
   */
  public function testUpdateCampaignReportback200() {
    $rb = array(
      'rbid' => 10,
      'quantity' => '1'
    );

    $response = $this->call('PUT', 'v1/campaigns/100/reportback', array(), array(), $this->server, json_encode($rb));
    $content = $response->getContent();

    // Response should return a 200
    $this->assertEquals(200, $response->getStatusCode());

    // Response should be valid JSON
    $this->assertJson($content);
  }

  /**
   * Test for update of a non-existent campaign report back.
   * PUT /campaigns/:nid/reportback
   *
   * @return void
   */
  public function testUpdateCampaignReportback401() {
    $rb = array(
      'rbid' => 11,
      'quantity' => '1'
    );

    $response = $this->call('PUT', 'v1/campaigns/100/reportback', array(), array(), $this->server, json_encode($rb));
    $content = $response->getContent();

    // Response should return a 401
    $this->assertEquals(401, $response->getStatusCode());

    // Response should be valid JSON
    $this->assertJson($content);
  }
}
