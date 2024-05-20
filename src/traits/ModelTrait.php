<?php

namespace src\traits;

trait ModelTrait
{

    //column cast to date format / retrieve from timestamp
    protected $dateColumn = ['created_at', 'updated_at'];
    protected $hidden = ['tableName'];

    public function show()
    {
        foreach ($this->hidden as $attr) {
            unset($this->{$attr});
        }

    }

}