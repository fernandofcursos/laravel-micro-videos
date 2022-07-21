<?php

namespace App\Models\Traits;

trait RulesGeneric
{

    protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        try {
            $model =[
                'name' => 'required|max:255',
                'is_active' => 'boolean'
            ];
        } catch(UnsatisfiedDependencyException $e) {
            abort(500, $e->getMessage());
        }
    });
}
}


