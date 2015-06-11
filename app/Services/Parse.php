<?php namespace Northstar\Services;

use Parse\ParseObject;
use Parse\ParseClient;
use Parse\ParsePush;

class Parse
{

    public function __construct()
    {
        $parse_app_id = config('services.parse.parse_app_id');
        $parse_api_key = config('services.parse.parse_api_key');
        $parse_master_key = config('services.parse.parse_master_key');

        ParseClient::initialize($parse_app_id, $parse_api_key, $parse_master_key);
    }


    public function sendPushNotification($data)
    {
        ParsePush::send(array(
            "channels" => ["PHPTest"],
            "data" => $data
        ));
    }
}
