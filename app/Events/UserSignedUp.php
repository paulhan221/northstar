<?php namespace Northstar\Events;

use Illuminate\Queue\SerializesModels;
use Northstar\Models\Campaign;

class UserSignedUp extends Event {

	use SerializesModels;

	public $campaign;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Campaign $campaign)
	{
		$this->signup_id = $campaign->signup_id;
	}

}
