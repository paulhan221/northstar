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
    $parameters = array('email' => 'test@dosomething.org',);
    $response = $this->call('GET', '1/users/campaigns', $parameters, array(), $this->server);
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
    // Campaign sid
    $sid = array('sid' => '235');

    $response = $this->call('POST', '1/campaigns/123/signup', array(), array(), $this->server, json_encode($sid));
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
   * Test for submiting a campaign reportback
   * POST /campaigns/:nid/reportback
   *
   * @return void
  */
  public function testSubmitCampaignReportback()
  {   
    // Campaign reportback data
    $rbid = array(
      'rbid' => '235',
      'quantity' => '3',
      'why_participated' => "I love helping others",
      'file_url' => 'happy.jpg'
    );

    $response = $this->call('POST', '1/campaigns/123/reportback', array(), array(), $this->server, json_encode($rbid));
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

}
