<?php namespace Northstar\Http\Controllers;

use Northstar\Services\DrupalAPI;
use Northstar\Models\User;
use Northstar\Models\Campaign;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CampaignController extends Controller
{

    /**
     * Drupal API wrapper.
     * @var DrupalAPI
     */
    protected $drupal;

    public function __construct(DrupalAPI $drupal)
    {
        $this->drupal = $drupal;
    }

    /**
     * Returns a user's campaigns
     * GET /users/:term/:id/campaigns
     *
     * @param $term string - Term to search by (eg. mobile, drupal_id, id, etc)
     * @param $id   string - The value to search for
     *
     * @return Response
     */
    public function show($term, $id)
    {
        // Type cast id fields as ints.
        if (strpos($term, '_id') !== false && $term !== '_id') {
            $id = (int)$id;
        }

        // Find the user.
        $user = User::where($term, $id)->first();

        if (!$user) {
            throw new NotFoundHttpException('The resource does not exist');
        }

        $campaigns = $user->campaigns;
        return response()->json($campaigns, 200);
    }


    /**
     * Sign user up for a given campaign.
     * POST /campaigns/:campaign_id/signup
     *
     * @param $campaign_id - Drupal campaign node ID
     * @param Request $request
     *
     * @return Response
     */
    public function signup($campaign_id, Request $request)
    {
        // Validate request body
        $this->validate($request, [
            'source' => ['required']
        ]);

        // Get the currently authenticated Northstar user.
        $user = User::current();

        // Return an error if the user doesn't exist.
        if (!$user->drupal_id) {
            throw new HttpException(401, 'The user must have a Drupal ID to sign up for a campaign.');
        }

        // Check if campaign signup already exists.
        $campaign = $user->campaigns()->where('drupal_id', $campaign_id)->first();

        if ($campaign) {
            throw new HttpException(401, 'Campaign signup already exists.');
        }

        // Create a Drupal signup via Drupal API, and store signup ID in Northstar.
        $signup_id = $this->drupal->campaignSignup($user->drupal_id, $campaign_id, $request->input('source'));

        // Save reference to the signup on the user object.
        $campaign = new Campaign;
        $campaign->drupal_id = $campaign_id;
        $campaign->signup_id = $signup_id;
        $campaign = $user->campaigns()->save($campaign);

        $response = array(
            'signup_id' => $campaign->signup_id,
            'created_at' => $campaign->created_at,
        );

        return response()->json($response, 201);
    }


    /**
     * Store a newly created campaign report back in storage.
     * POST /campaigns/:campaign_id/reportback
     *
     * @param $campaign_id - Drupal campaign node ID
     * @param Request $request
     *
     * @return Response
     */
    public function reportback($campaign_id, Request $request)
    {
        // Validate request body
        $this->validate($request, [
            'quantity' => ['required', 'integer'],
            'why_participated' => ['required'],
            'file' => ['required', 'string'], // Data URL!
            'caption' => ['string']
        ]);

        // Get the currently authenticated Northstar user.
        $user = User::current();

        // Return an error if the user doesn't exist.
        if (!$user->drupal_id) {
            throw new HttpException(401, 'The user must have a Drupal ID to submit a reportback.');
        }

        // Check if campaign signup already exists.
        $campaign = $user->campaigns()->where('drupal_id', $campaign_id)->first();

        if (!$campaign) {
            throw new HttpException(401, 'User is not signed up for this campaign yet.');
        }

        // Create a reportback via the Drupal API, and store reportback ID in Northstar
        $reportback_id = $this->drupal->campaignReportback($user->drupal_id, $campaign_id, [
            'quantity' => $request->input('quantity'),
            'why_participated' => $request->input('why_participated'),
            'file' => $request->input('file'),
            'caption' => $request->input('caption')
        ]);

        $campaign->reportback_id = $reportback_id;
        $campaign->save();

        return response()->json(['reportback_id' => $reportback_id, 'created_at' => $campaign->updated_at], 201);
    }

    /**
     * Update a campaign report back in storage.
     * PUT /campaigns/:campaign_id/reportback
     *
     * @return Response
     */
    public function updateReportback($campaign_id)
    {
        throw new HttpException(501, 'Not yet implemented.');

        // ...
    }

}
