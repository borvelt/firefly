<?php
use Illuminate\Database\Eloquent\Model as Model;
class Download extends Model
{
    public function __construct(array $attributes = array())
    {
        Bootstrap::Eloquent();
        parent::__construct($attributes);
    }

}
