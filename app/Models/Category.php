<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RulesGeneric;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes, Uuid;
    

    protected $fillable = [
        'name', 'description', 'is_active'
    ];
    protected $dates=['delete_at'];



}
