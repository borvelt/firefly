<?php
use Illuminate\Database\Eloquent\Model as Model;
class Schedule extends Model
{
    protected $fillable = ['bulk', 'limitation'];

    public function __construct(array $attributes = array())
    {
        Bootstrap::Eloquent();
        parent::__construct($attributes);
    }
}
