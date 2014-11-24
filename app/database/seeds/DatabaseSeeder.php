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
        	'drupal_uid' => 123456,
        	'doc_id' => Hash::make("123abc"),
        	'first_name' => 'First',
        	'last_name' => 'Last',
            'campaigns' => array(
            	array(
            			"nid" => 123,
            			"rbid" => 100,
            			"sid" => 100
            	),
            	array(
            			"nid" => 456,
            			"sid" => 101
            	))
        )); 

        User::create(array(
        	'email' => 'example@dosomething.org', 
        	'drupal_uid' => 789542,
        	'doc_id' => Hash::make("456def"),
        	'mobile' => '3333333333',
            'first_name' => 'John',
        	'last_name' => 'Doe',
            'campaigns' => array(
            	array(
            			"nid" => 555,
            			"rbid" => 200,
            			"sid" => 200
            	),
            	array(
            			"nid" => 124,
            			"sid" => 201
            	))
        )); 
    }

}