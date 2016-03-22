<?php
use Illuminate\Database\Eloquent\Model as Model;
class Authorization extends Model
{
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at', 'pivot'];
    public function __construct(array $attributes = array())
    {
        Bootstrap::Eloquent();
        parent::__construct($attributes);
    }
}
