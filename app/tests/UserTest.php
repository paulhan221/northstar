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
        Route::enableFilters();

        Artisan::call('migrate');
        $this->seed();

        $this->server = array(
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X-DS-Application-Id' => '456',
            'HTTP_X-DS-REST-API-Key' => 'abc4324'
        );

    }


    /**
     * Test for retrieving a user 
     * GET /users
     *
     * @return void
     */
    public function testGetDataFromUser()
    {   

        $parameters = array('email' => 'test@dosomething.org',);
        $response = $this->call('GET', '1/users', $parameters, array(), $this->server);
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

    /**
     * Test for registering a new user 
     * POST /users
     *
     * @return void
     */
    public function testRegisterUser()
    {

        // Create a new user object
        $user = array(
            'email' => 'new@dosomething.org',
            'mobile' => '5556667777',
            'password' => 'secret',
        );

        $response = $this->call('POST', '1/users', array(), array(), $this->server, json_encode($user));
        $content = $response->getContent();
        $data = json_decode($content, true);

        // The response should return a 201 Created status code
        $this->assertEquals(201, $response->getStatusCode());

        // Response should be valid JSON
        $this->assertJson($content);

        // Response should return created at and id columns
        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('_id', $data);
        
    }

    /**
     * Test for updating an existing user 
     * PUT /users
     *
     * @return void
     */
    public function testUpdateUser()
    {

        // Create a new user object
        $user = array(
            'email' => 'newemail@dosomething.org'
        );

        $response = $this->call('PUT', '1/users/5480c950bffebc651c8b456f', array(), array(), $this->server, json_encode($user));
        $content = $response->getContent();
        $data = json_decode($content, true);

        // The response should return a 202 Accepted status code
        $this->assertEquals(202, $response->getStatusCode());

        // Response should be valid JSON
        $this->assertJson($content);

        // Response should return created at and id columns
        $this->assertArrayHasKey('updated_at', $data);
        
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

        $response = $this->call('POST', '1/login', array(), array(), $this->server, json_encode($credentials));
        $content = $response->getContent();
        $data = json_decode($content, true);

        // The response should return a 201 Created status code
        $this->assertEquals(200, $response->getStatusCode());

        // Response should be valid JSON
        $this->assertJson($content);

        // Response should return created at and id columns
        $this->assertArrayHasKey('session_token', $data);

    }

}
