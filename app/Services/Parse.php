<?php namespace Northstar\Services;

use Parse\ParseObject;
use Parse\ParseClient;
use Parse\ParsePush;
use Parse\ParseInstallation;

class Parse
{

    public function __construct()
    {
        $parse_app_id = config('services.parse.parse_app_id');
        $parse_api_key = config('services.parse.parse_api_key');
        $parse_master_key = config('services.parse.parse_master_key');

        ParseClient::initialize($parse_app_id, $parse_api_key, $parse_master_key);
    }


    public function sendPushNotification($installation_ids, $data)
    {
        // Loop through the installation ids
        foreach ($installation_ids as $id) {
            $query = ParseInstallation::query();
            $query->equalTo("installationId", $id);
            ParsePush::send(array(
                "where" => $query,
                "data" => $data
            ));
        }
    }
}
