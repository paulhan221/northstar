<?php namespace Northstar\Events;

use Illuminate\Queue\SerializesModels;
use Northstar\Models\User;

class UserGotKudo extends Event {

	use SerializesModels;

	public $user;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(User $user)
	{
		$this->user = $user;
	}

}
