<?php namespace Northstar\Handlers\Events;

use Northstar\Events\UserReportedBack;
use Northstar\Models\User;
use Northstar\Services\DrupalAPI;
use Northstar\Services\Parse;


class SendReportbackPushNotification {

    /**
     * Parse API wrapper.
     * @var Parse
     */
    protected $parse;

    /**
     * Drupal API wrapper
     * @var DrupalAPI
     */
    protected $drupal;

    /**
     * Create the event handler.
     *
     * @return void
     */
    public function __construct(Parse $parse, DrupalAPI $drupal)
    {
        $this->parse = $parse;
        $this->drupal = $drupal;
    }

    /**
     * Handle the event.
     *
     * @param  UserReportedBack  $event
     * @return void
     */
    public function handle(UserReportedBack $event)
    {
        $group = User::group($event->campaign->signup_group);

        // If group count is only 1, safe to assume the 1 user is the one who reported back
        if (count($group) <= 1) {
            return;
        }

        // Get reportback content
        $reportback_response = $this->drupal->reportbackContent($event->campaign->reportback_id);

        // Assuming the last item in this array is the latest reportback submitted
        $reportback_items = $reportback_response['data']['reportback_items']['data']['total'];
        $latest_item = $reportback_response['data']['reportback_items']['data'][$reportback_items - 1];

        // Loop through the users in the group.
        foreach ($group as $user) {
            $drupal_id = $user->drupal_id;
            // Check that this user is not the user that triggered the event.
            if ($drupal_id !== $event->user->drupal_id && !empty($user->parse_installation_ids)) {
                // @TODO The user id and campaign id need to be turned into user name and campaign name
                $message = $event->user->drupal_id . ' shared a photo in your ' . $event->campaign->drupal_id . ' group.';

                // @TODO group.data.users doesn't include detailed reportback info for each user, is that ok?
                $data = [
                    'alert' => $message,
                    'extras' => [
                        'completion' => [
                            'message' => $message,
                        ],
                        'group' => [
                            'data' => [
                                'campaign_id' => $event->campaign->drupal_id,
                                'users' => $group,
                            ]
                        ],
                        'reportback_items' => [
                            'total' => 1,
                            'data' => [$latest_item],
                        ],
                    ],
                ];

                // Send notifications to the user's devices.
                $this->parse->sendPushNotification($user->parse_installation_ids, $data);
            }
        }
    }
}
