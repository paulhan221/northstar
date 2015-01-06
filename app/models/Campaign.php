<?php

use Jenssegers\Mongodb\Model as Eloquent;

class Campaign extends Eloquent {

  protected $primaryKey = "_id";

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = array('_id');

  /**
   * Validation rules
   */
  private $rules = array(
    REPORTBACK_ATTRIBUTE::rbid => 'integer',
    REPORTBACK_ATTRIBUTE::file_url => 'url',
    REPORTBACK_ATTRIBUTE::quantity => 'integer',

    SIGNUP_ATTRIBUTE::sid => 'integer',
  );

  /**
   * Messages returned from a failed validation.
   */
  private $validationMessages;

  /**
   * Automatically convert date columns to instances of Carbon
   *
   */
  public function getDates()
  {
    return array('created_at', 'updated_at');
  }

  /**
   * Validate input based on this model's rules.
   *
   * @param $data
   * @return bool
   */
  public function validate($data)
  {
    $v = Validator::make($data, $this->rules);

    if ($v->fails()) {
      $this->validationMessages = $v->messages()->all();
      return false;
    }

    return true;
  }

  /**
   * Get validation messages.
   *
   * @return array
   */
  public function getValidationMessages()
  {
    return $this->validationMessages;
  }

  /**
   * Formats date if its a MongoDate.
   *
   * @param $value date attribute value
   * @return String
   */
  private function formatDate($value) {
    $date = $this->asDateTime($value);
    return $date->format('Y-m-d H:i:s');
  }

  /**
   * Accessor for created_at date. Formats to Y-m-d H:i:s.
   */
  public function getCreatedAtAttribute($value) {
    return $this->formatDate($value);
  }

  /**
   * Accessor for updated_at date. Formats to Y-m-d H:i:s.
   */
  public function getUpdatedAtAttribute($value) {
    return $this->formatDate($value);
  }

  public function getQuantityAttribute($value) {
    return (int) $value;
  }

  public function setQuantityAttribute($value) {
    $this->attributes[REPORTBACK_ATTRIBUTE::quantity] = (int) $value;
  }

  public function getRbidAttribute($value) {
    return (int) $value;
  }

  public function setRbidAttribute($value) {
    $this->attributes[REPORTBACK_ATTRIBUTE::rbid] = (int) $value;
  }

  public function getSidAttribute($value) {
    return (int) $value;
  }

  public function setSidAttribute($value) {
    $this->attributes[SIGNUP_ATTRIBUTE::sid] = (int) $value;
  }

}

abstract class REPORTBACK_ATTRIBUTE {
  const rbid = 'rbid';

  const file_url = 'file_url';
  const quantity = 'quantity';
  const why_participated = 'why_participated';

  public static function editableKeys()
  {
    return array(
      REPORTBACK_ATTRIBUTE::file_url,
      REPORTBACK_ATTRIBUTE::quantity,
      REPORTBACK_ATTRIBUTE::why_participated
    );
  }
}

abstract class SIGNUP_ATTRIBUTE {
  const sid = 'sid';
}