<?php

use Jenssegers\Mongodb\Model as Eloquent;

class Campaign extends Eloquent {

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('_id', 'created_at','updated_at');

}
