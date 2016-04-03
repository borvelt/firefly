<?php
use Illuminate\Database\Eloquent\Model as Model;
class Book extends Model
{
    protected $with;
    protected $guarded = ['*'];
    public function __construct(array $attributes = array())
    {
        Bootstrap::Eloquent();
        parent::__construct($attributes);
    }

}
