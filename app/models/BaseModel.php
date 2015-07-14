<?php

namespace Models;

class BaseModel extends \Eloquent
{
    public static function conflicts($value, $field)
    {
        return (static::where($field, '=', $value)->count() != 0);
    }
}