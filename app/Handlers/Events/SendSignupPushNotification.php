<?php namespace Northstar\Handlers\Events;

use Northstar\Events\UserSignedUp;
use Northstar\Models\User;

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
	 *
	 * @param  UserSignedUp  $event
	 * @return void
	 */
	public function handle(UserSignedUp $event)
	{

    // @TODO - Make sure group is not empty.
    // @TODO - We might not need signup_source here, be sure to remove if it is not used.

		// Get signup group.
		$group = User::where('campaigns', 'elemMatch', ['signup_id' => (int)$event->signup_id])
                        ->orWhere('campaigns', 'elemMatch', ['signup_source' => $event->signup_id])->get();

    // Loop through the users in the group.
    foreach ($group as $user) {
    	// Get this users sign up id.
    	$user_signup_id = $user->campaigns[0]->signup_id;

    	// Check that this user is not the user that triggered the event.
    	if ($user_signup_id !== $event->signup_id) {
    		// Send push notification to this user.
    	}
    }
	}

}
