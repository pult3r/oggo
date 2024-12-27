<?php

namespace App\Casts;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Carbon\Carbon;

class DateCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return (isset($value)) ? Carbon::parse( $value )->format('d.m.Y') : null ; 
    }

    public function set($model, $key, $value, $attributes)
    {
        return (isset($value)) ? Carbon::parse( $value ) : null;
    }
}