<?php namespace Northstar\Handlers\Events;

use Northstar\Events\UserSignedUp;
use Northstar\Models\User;

use Parse\ParseObject;
use Parse\ParseClient;
use Parse\ParsePush;


// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Contracts\Queue\ShouldBeQueued;

class SendSignupPushNotification {

	/**
	 * Create the event handler.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 * @param  UserSignedUp  $event
	 * @return void
	 */
	public function handle(UserSignedUp $event)
	{

		$parse_app_id = config('services.parse.parse_app_id');
		$parse_api_key = config('services.parse.parse_api_key');
		$parse_master_key = config('services.parse.parse_master_key');

		ParseClient::initialize($parse_app_id, $parse_api_key, $parse_master_key);

    // @TODO - Make sure group is not empty.
    // @TODO - We might not need signup_source here, be sure to remove if it is not used.

		// // Get signup group.
		$group = User::where('campaigns', 'elemMatch', ['signup_id' => (int)$event->signup_id])
						->orWhere('campaigns', 'elemMatch', ['signup_source' => $event->signup_id])->get();

    // Loop through the users in the group.
    foreach ($group as $user) {
    	// Get this users sign up id.
    	$user_signup_id = $user->campaigns[0]->signup_id;

    	// Check that this user is not the user that triggered the event.
    	if ($user_signup_id == $event->signup_id) {
				$data = array("alert" => "Hi!");

				ParsePush::send(array(
				  "channels" => ["PHPTest"],
				  "data" => $data
				));
    	}
    }
	}

}
