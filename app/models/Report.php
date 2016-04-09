<?php
use Illuminate\Database\Eloquent\Model as Model;
class Report extends Model
{

    protected $fillable = ['book', 'ip'];

    public function __construct(array $attributes = array())
    {
        Bootstrap::Eloquent();
        parent::__construct($attributes);
    }
}
