<?php

use Jenssegers\Mongodb\Model as Eloquent;

class Campaign extends Eloquent {


  /**
   * Guarded attributes
   */
  protected $guarded = array('_id');

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = ['_id'];

  /**
   * Validation rules
   */
  private $rules = [
    'rbid' => 'integer',
    'file_url' => 'url',
    'quantity' => 'integer',
    'sid' => 'integer',
  ];

  /**
   * Bag of messages returned from a failed validation.
   */
  private $validationMessages;

  /**
   * Automatically convert date columns to instances of Carbon
   */
  public function getDates()
  {
    return ['created_at', 'updated_at'];
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
   * @param $value mixed - date attribute value
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
    $this->attributes['quantity'] = (int) $value;
  }

  public function getRbidAttribute($value) {
    return (int) $value;
  }

  public function setRbidAttribute($value) {
    $this->attributes['rbid'] = (int) $value;
  }

  public function getSidAttribute($value) {
    return (int) $value;
  }

  public function setSidAttribute($value) {
    $this->attributes['sid'] = (int) $value;
  }

}
