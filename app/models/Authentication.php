<?php
use Illuminate\Database\Eloquent\Model as Model;
class Authentication extends Model
{
  public function __construct(array $attributes = array())
  {
    Bootstrap::Eloquent();
    parent::__construct($attributes);
  }
  public function user ()
  {
    return $this->belongsTo('User', 'user');
  }
}
