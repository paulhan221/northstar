<?php

class UserTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testGetUser()
	{
		$crawler = $this->client->request('GET', '1/users?email=test@dosomething.org');
        $this->assertTrue($this->client->getResponse()->isOk());
	}

}
