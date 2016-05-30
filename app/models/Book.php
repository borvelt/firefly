<?php
use Illuminate\Database\Eloquent\Model as Model;
class Book extends Model
{

    protected $appends = ['timeAddedSolar'];

    public function __construct(array $attributes = array())
    {
        Bootstrap::Eloquent();
        parent::__construct($attributes);
    }
    public function getTimeAddedSolarAttribute () {
  		return jdate("Y-m-d H:i:s", strtotime($this->timeAdded));
  	}

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }


}
