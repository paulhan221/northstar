<?php namespace Northstar\Handlers\Events;

use Northstar\Events\UserSignedUp;
use Northstar\Models\User;

use Northstar\Services\Parse;

class SendSignupPushNotification {

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
   * @param  UserSignedUp  $event
   * @return void
   */
  public function handle(UserSignedUp $event)
  {
    // Get signup group.
    $group = User::where('campaigns', 'elemMatch', ['signup_id' => (int)$event->signup_id])
            ->orWhere('campaigns', 'elemMatch', ['signup_source' => $event->signup_id])->get();

    if (count($group) > 0) {
      // Loop through the users in the group.
      foreach ($group as $user) {
        $drupal_id = $user->drupal_id;
        // Check that this user is not the user that triggered the event.
        if ($drupal_id !== $event->drupal_id) {
          // @TODO - This is placeholder content.
          $data = array("alert" => "I just signed up to your group");

          // Send notifications to the users devices.
          if (!empty($user->parse_installation_ids)) {
            $this->parse->sendPushNotification($user->parse_installation_ids, $data);
          }
        }
      }
    }
  }

}
