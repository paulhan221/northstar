<?php

use Northstar\Models\Token;

class AuthTest extends TestCase
{

    /**
     * Migrate database and set up HTTP headers
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
        $this->seed();

        $this->server = array(
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Accept' => 'application/json',
            'HTTP_X-DS-Application-Id' => '456',
            'HTTP_X-DS-REST-API-Key' => 'abc4324',
            'HTTP_Session' => 'S0FyZmlRNmVpMzVsSzJMNUFreEFWa3g0RHBMWlJRd0tiQmhSRUNxWXh6cz0='
        );

        $this->serverForParseTest = array(
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Accept' => 'application/json',
            'HTTP_X-DS-Application-Id' => '456',
            'HTTP_X-DS-REST-API-Key' => 'abc4324',
            'HTTP_Session' => 'S0FyZmlRNmVpMzVsSzJMNUFreEFWa3g0RHBMWlJRd0tiQmhSRUNxWXh6cz1='
        );

        $this->serverMissingToken = array(
            'HTTP_Accept' => 'application/json',
            'HTTP_X-DS-Application-Id' => '456',
            'HTTP_X-DS-REST-API-Key' => 'abc4324',
        );

        $this->serverFakeToken = array(
            'HTTP_Accept' => 'application/json',
            'HTTP_X-DS-Application-Id' => '456',
            'HTTP_X-DS-REST-API-Key' => 'abc4324',
            'HTTP_Session' => 'thisisafaketoken',
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

        $response = $this->call('POST', 'v1/login', [], [], [], $this->server, json_encode($credentials));
        $content = $response->getContent();
        $data = json_decode($content, true);

        // The response should return a 200 Created status code
        $this->assertEquals(200, $response->getStatusCode());

        // Response should be valid JSON
        $this->assertJson($content);

        // Response should include user ID & session token
        $this->assertArrayHasKey('_id', $data['data']);
        $this->assertArrayHasKey('session_token', $data['data']);

        // Assert token exists in database
        $tokenCount = Token::where('key', '=', $data['data']['session_token'])->count();
        $this->assertEquals($tokenCount, 1);
    }

    /**
     * Test for logging out a user
     * POST /logout
     *
     * @return void
     */
    public function testLogout()
    {
        $response = $this->call('POST', 'v1/logout', [], [], [], $this->server);
        $content = $response->getContent();

        // The response should return a 200 Created status code
        $this->assertEquals(200, $response->getStatusCode());

        // Response should be valid JSON
        $this->assertJson($content);
    }

    /**
     * Tests that when a user gets logged out, we can also remove the
     * Parse installation id from the user doc.
     * POST /logout
     *
     * @return void
     */
    public function testLogoutRemovesParseInstallationIds()
    {
        $payload = array(
            'parse_installation_ids' => 'parse-abc123'
        );

        $logoutResponse = $this->call('POST', 'v1/logout', [], [], [], $this->serverForParseTest, json_encode($payload));

        // The response should return a 200 Created status code
        $this->assertEquals(200, $logoutResponse->getStatusCode());

        // Verify parse_installation_ids got removed from the user
        $getResponse = $this->call('GET', 'v1/users/_id/bf1039b0271bcc636aa5477c', [], [], [], $this->serverForParseTest);
        $getContent = $getResponse->getContent();
        $user = json_decode($getContent, true);

        $this->assertEquals(0, count($user['data'][0]['parse_installation_ids']));
    }

    /**
     * Tests that a proper error is thrown when a route requiring an auth token
     * is given no token.
     */
    public function testMissingToken() {
        $response = $this->call('GET', 'v1/user/campaigns/123', [], [], [], $this->serverMissingToken);

        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Tests that a proper error is thrown when a route requiring an auth token
     * is given a fake token.
     */
    public function testFakeToken() {
        $response = $this->call('GET', 'v1/user/campaigns/123', [], [], [], $this->serverFakeToken);

        $this->assertEquals(401, $response->getStatusCode());
    }
}
