<?php

use Illuminate\Database\Seeder;
use Northstar\Models\ApiKey;

class ApiKeyTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('api_keys')->delete();

        ApiKey::create(array(
            'app_id' => '456',
            'api_key' => 'abc4324'
        ));

        ApiKey::create(array(
            'app_id' => '123',
            'api_key' => '5464utyrs'
        ));

    }

}
