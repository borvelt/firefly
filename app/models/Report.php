<?php
use Illuminate\Database\Eloquent\Model as Model;
class Report extends Model
{

    protected $fillable = ['book', 'ip'];
    protected $appends = ['created_at_solar'];
    protected $with = ['book'];

    public function __construct(array $attributes = array())
    {
        Bootstrap::Eloquent();
        parent::__construct($attributes);
    }

    public function book () {
        return $this->belongsTo("Book", "book", "id");
    }

    public function getCreatedAtSolarAttribute () {
        return jdate("Y-m-d H:i:s", strtotime($this->created_at));
    }

}
