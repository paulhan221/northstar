<?php

class UserTest extends TestCase {

	/**
	 * Migrate database
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();

		Artisan::call('migrate');
		$this->seed();
	}


	/**
	 * Test for retrieving a user 
	 * GET /users
	 *
	 * @return void
	 */
	public function testGetDataFromUser()
	{
	    $response = $this->call('GET', '1/users?email=test@dosomething.org');
		$content = $response->getContent();

		// The response should return a 200 OK status code
		$this->assertEquals(200, $response->getStatusCode());
		
		// Response should be valid JSON
		$this->assertJson($content);
	}

	/**
	 * Test for retrieving a nonexistant User 
	 * GET /users
	 *
	 * @return void
	 */
	public function testGetData()
	{
	    $response = $this->call('GET', '1/users');
		$content = $response->getContent();

		// The response should return a 404 Not Found status code
		$this->assertEquals(404, $response->getStatusCode());
		
	}

}
