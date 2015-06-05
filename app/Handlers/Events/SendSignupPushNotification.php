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
		// $event->signup_id;
		// Get signup group.
		// $group = User::where('campaigns', 'elemMatch', ['signup_id' => (int)$event->signup_id])
                        //->orWhere('campaigns', 'elemMatch', ['signup_source' => $event->signup_id])->get();

		// I now have access to signup id and sign up source.
		// - Add check that signup source is a number.
		// - If it is a number get all the other users in that group (see above);
		// - Send push notification to those users.
    return 'signup_id = ' . $event->signup_id . " signup_source = " . $event->signup_source;
	}

}
