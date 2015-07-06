<?php

namespace Northstar\Http\Middleware;

use Northstar\Models\User;
use Closure;

class UserResponseMiddleware {

    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response = $response->getData();

        // Ensure user objects have all fillable properties set with
        if (is_array($response->data)) {
            foreach ($response->data as $user) {
                self::fillWithNullValues($user);
            }
        }
        else if (is_object($response->data)) {
            self::fillWithNullValues($response->data);
        }

        return json_encode($response);
    }

    /**
     * For any fillable User properties that are not hidden, set values to keys
     * that are unset to null.
     *
     * @param $user User object
     */
    private function fillWithNullValues(&$user)
    {
        $tmp = new User();

        $fillableNotHidden = array_diff($tmp->getFillable(), $tmp->getHidden());

        foreach ($fillableNotHidden as $key) {
            $user->$key = isset($user->$key) ?: null;
        }
    }

}