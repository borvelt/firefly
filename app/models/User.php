<?php

use Codesleeve\Stapler\ORM\EloquentTrait;
use Codesleeve\Stapler\ORM\StaplerableInterface;
use Illuminate\Database\Eloquent\Model as Model;

class User extends Model implements StaplerableInterface
{
    use EloquentTrait;
    public static $_with = [];
    protected $guarded = ['*'];
    protected $hidden = ['password'];
    public function __construct(array $attributes = array())
    {
        Bootstrap::Eloquent();
        Bootstrap::Stapler();
        $this->hasAttachedFile('avatar_attachment', [
            'styles' => [
                'thumbnail' => '100x100',
            ],
            'url' => '/web/system/:attachment/:id_partition/:style/:filename',
            'default_url' => '/web/system/:attachment/:style/missing.jpg',
        ]);
        parent::__construct($attributes);
    }
    /*
    @ Overloading save method for Stapler.
     */
    public function save(array $data = array())
    {
        if (parent::save()) {
            $this->avatar_attachment->save();
            return true;
        }
        return false;
    }
    public function authenticate()
    {
        return $this->belongsTo('Authentication', 'authenticate');
    }
    public function authorize()
    {
        return $this->belongsToMany('Authorization', 'user_authorization', 'user', 'authorization');
    }
    public function avatar($type = "thumbnail")
    {
        if (is_array($type) && count($type)) {
            $return = [];
            foreach ($type as $typ) {
                $return[] = $this->avatar_attachment->url($typ);
            }
            return $return;
        }
        return $this->avatar_attachment->url($type);
    }
}
