<?php

use Jenssegers\Mongodb\Model as Eloquent;

class Campaign extends Eloquent {

  protected $primaryKey = "_id";

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
  */
  protected $hidden = array('_id', 'created_at', 'updated_at');

  /*
   * Automatically convert date columns to instances of Carbon
   *
  */
  public function getDates()
  {
    return array('created_at', 'updated_at');
  }

}
