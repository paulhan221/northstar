<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UserTableSeeder');
		$this->command->info('User table seeded successfully!');
	}

}


class UserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();

        User::create(array(
            'email' => 'test@dosomething.org', 
            'mobile' => '5555555555',
            'password' => 'secret',
            'drupal_uid' => 123456,
            'addr_street1' => '123',
            'addr_street2' => '456',
            'addr_city' => 'Paris',
            'addr_state' => 'Florida',
            'addr_zip' => '555555',
            'country' => 'US',
            'birthdate' => '12/17/91',
            'first_name' => 'First',
            'last_name' => 'Last'
        )); 

        User::create(array(
            'email' => 'info@dosomething.org', 
            'mobile' => '5556669999',
            'password' => 'secret',
            'drupal_uid' => 456788,
            'addr_street1' => '456',
            'addr_street2' => '33',
            'addr_city' => 'Example',
            'addr_state' => 'Testing',
            'addr_zip' => '555555',
            'country' => 'US',
            'birthdate' => '12/17/91',
            'first_name' => 'John',
            'last_name' => 'Doe'
        )); 
    }

}