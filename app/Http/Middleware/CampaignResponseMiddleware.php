<?php

namespace Northstar\Http\Middleware;

use Closure;
use Northstar\Models\Campaign;

class CampaignResponseMiddleware {

    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response = $response->getData();

        if (is_array($response->data)) {
            foreach ($response->data as $campaign) {
                self::fillCampaign($campaign);
            }
        } elseif (is_object($response->data)) {
            self::fillCampaign($response->data);
        }

        return json_encode($response);
    }

    /**
     * For all Campaign attributes not hidden, where keys are unset, set those
     * value to null.
     *
     * @param $campaign User campaign activity data
     */
    private function fillCampaign(&$campaign)
    {
        $tmp = new Campaign();

        $attrsNotHidden = array_diff($tmp->getAttributes(), $tmp->getHidden());

        foreach ($attrsNotHidden as $key => $value) {
            $campaign->$key = isset($campaign->$key) ? $campaign->$key : null;
        }
    }
}