<?php namespace Northstar\Events;

use Illuminate\Queue\SerializesModels;
use Northstar\Models\User;

class UserSignedUp extends Event {

	use SerializesModels;

	public $user;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(User $user)
	{
		$this->drupal_id = $user->drupal_id;
		$this->signup_id = $user->campaigns[0]->signup_id;
		$this->signup_source = $user->campaigns[0]->signup_source;
	}

}
