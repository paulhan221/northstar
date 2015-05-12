<?php

use Illuminate\Database\Seeder;
use Northstar\Models\User;

class UserTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // @TODO: Why is this being called... it's called for each unit test.
        // Without this line, the unit tests fail.
        DB::table('users')->delete();

        // Non-signed up user
        User::create([
            '_id' => '5430e850dt8hbc541c37tt3d',
            'email' => 'test@dosomething.org',
            'mobile' => '5555555555',
            'password' => 'secret',
            'drupal_id' => 123456,
            'addr_street1' => '123',
            'addr_street2' => '456',
            'addr_city' => 'Paris',
            'addr_state' => 'Florida',
            'addr_zip' => '555555',
            'country' => 'US',
            'birthdate' => '12/17/91',
            'first_name' => 'First',
            'last_name' => 'Last',
        ]);

        // Signed up user
        User::create([
            '_id' => '5480c950bffebc651c8b456f',
            'email' => 'test2@dosomething.org',
            'mobile' => '5554445555',
            'password' => 'secret',
            'drupal_id' => 123457,
            'addr_street1' => '123',
            'addr_street2' => '456',
            'addr_city' => 'Paris',
            'addr_state' => 'Florida',
            'addr_zip' => '555555',
            'country' => 'US',
            'birthdate' => '12/17/91',
            'first_name' => 'First',
            'last_name' => 'Last',
            'campaigns' => [
                [
                    '_id' => '5480c950bffebc651c8b456e',
                    'drupal_id' => 123,
                    'signup_id' => 100
                ]
            ]
        ]);

        User::create(array(
            'email' => 'info@dosomething.org',
            'mobile' => '5556669999',
            'password' => 'secret',
            'drupal_id' => 456788,
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

        User::create(array(
            '_id' => '5480c950bffebc651c8b4570',
            'email' => 'delete-test@ds.org',
            'password' => 'secret'
        ));

        if (App::environment('local')) {
            $faker = Faker\Factory::create();
            foreach (range(1, 50) as $index) {
                User::create([
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $faker->unique()->safeEmail,
                    'mobile' => $faker->unique()->phoneNumber,
                    'password' => 'secret',
                    'birthdate' => $faker->date($format = 'm/d/Y', $max = 'now'),
                    'addr_street1' => $faker->streetAddress,
                    'addr_street2' => $faker->secondaryAddress,
                    'city' => $faker->city,
                    'addr_state' => $faker->state,
                    'addr_zip' => $faker->postcode,
                    'country' => $faker->country,
                    'cgg_id' => $index,
                ]);
            }
        }
    }

}
