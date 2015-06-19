<?php namespace Northstar\Handlers\Events;

use Northstar\Events\UserGotKudo;
use Northstar\Models\User;

use Northstar\Services\Parse;

class SendKudoPushNotification {

  /**
   * Parse API wrapper.
   * @var Parse
   */
  protected $parse;

  /**
   * Create the event handler.
   *
   * @return void
   */
  public function __construct(Parse $parse)
  {
    $this->parse = $parse;
  }


	/**
	 * Handle the event.
	 *
	 * @param  UserGotKudo  $event
	 * @return void
	 */
	public function handle(UserGotKudo $event)
	{
    // @TODO - This is placeholder content.
    $data = array("alert" => "You just got a kudo");

    // Send notifications to the users devices.
    if (!empty($user->parse_installation_ids)) {
      $this->parse->sendPushNotification($user->parse_installation_ids, $data);
    }
	}

}
