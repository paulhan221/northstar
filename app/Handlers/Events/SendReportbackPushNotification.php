<?php namespace Northstar\Handlers\Events;

use Northstar\Events\UserReportedBack;
use Northstar\Models\User;

use Northstar\Services\Parse;


class SendReportbackPushNotification {

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
	 * @param  UserReportedBack  $event
	 * @return void
	 */
	public function handle(UserReportedBack $event)
	{

		$group = User::group($event->campaign->signup_id);

	  if (count($group) > 0) {
	    // Loop through the users in the group.
	    foreach ($group as $user) {
	      $drupal_id = $user->drupal_id;
	      // Check that this user is not the user that triggered the event.
	      if ($drupal_id !== $event->user->drupal_id) {
	        // @TODO - This is placeholder content.
	        $data = array("alert" => "A user in your group just reported back");

	        // Send notifications to the users devices.
	        if (!empty($user->parse_installation_ids)) {
	          $this->parse->sendPushNotification($user->parse_installation_ids, $data);
	        }
	      }
	    }
	  }
	}
}
